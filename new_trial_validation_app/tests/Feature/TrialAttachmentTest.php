<?php

use App\Models\ActivityLog;
use App\Models\MasterOption;
use App\Models\Trial;
use App\Models\TrialAttachmentFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

function makeAttachmentTrial(array $attributes = []): Trial
{
    return Trial::create([
        'trial_code' => $attributes['trial_code'] ?? 'TRIAL-ATTACH-1',
        'product_name' => 'Sample Product',
        'product_type' => $attributes['product_type'] ?? 'Tube',
        'progress_status' => $attributes['progress_status'] ?? 'Draft',
        'current_step' => $attributes['current_step'] ?? 'Attachment',
        'created_by' => $attributes['created_by'] ?? 'owner@local.test',
    ]);
}

function makeAttachmentCategory(string $name = 'Vacuum Test'): MasterOption
{
    return MasterOption::create([
        'type' => 'attachment_category',
        'name' => $name,
        'sort_order' => 0,
        'is_active' => true,
    ]);
}

test('a non-draft trial attachments page is viewable by a viewer with canEdit false', function () {
    $viewer = User::factory()->role('Viewer')->create();
    $trial = makeAttachmentTrial(['progress_status' => 'In Review']);

    $response = $this->actingAs($viewer)->get(route('trials.attachments.edit', $trial));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('canEdit', false));
});

test('a user with no view access is forbidden', function () {
    // A role/department that isn't Staff/Viewer/an approver role and isn't a
    // reviewer-department code either (User::reviewerDepartmentCodes()), so
    // TrialPolicy::view() falls straight through to its final `return false`
    // without needing a trials_review row (that table has no fresh-install
    // migration in this app yet).
    $outsider = User::factory()->create(['role' => 'Random Role', 'department' => 'Nowhere']);
    $trial = makeAttachmentTrial(['progress_status' => 'In Review']);

    $this->actingAs($outsider)->get(route('trials.attachments.edit', $trial))->assertForbidden();
});

test('a soft-deleted trial 404s on the attachments page', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email]);
    $trial->deleted_at = Carbon::now();
    $trial->save();

    $this->actingAs($owner)->get(route('trials.attachments.edit', $trial))->assertNotFound();
});

test('uploading valid photos persists rows, advances current_step, and writes an activity log', function () {
    Storage::fake('legacy_uploads');

    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email, 'current_step' => 'WeighingFilling']);
    makeAttachmentCategory('Vacuum Test');

    $response = $this->actingAs($owner)->post(route('trials.attachments.store', $trial), [
        'category' => 'Vacuum Test',
        'photos' => [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.png'),
        ],
    ]);

    $response->assertRedirect(route('trials.attachments.edit', $trial));

    $files = TrialAttachmentFile::where('trial_id', $trial->id)->get();
    expect($files)->toHaveCount(2);
    expect($files->every(fn (TrialAttachmentFile $f) => $f->category === 'Vacuum Test'))->toBeTrue();
    expect($files->every(fn (TrialAttachmentFile $f) => $f->uploaded_by === 'owner@local.test'))->toBeTrue();

    foreach ($files as $file) {
        Storage::disk('legacy_uploads')->assertExists($trial->id.'/'.$file->file_name);
    }

    expect($trial->fresh()->current_step)->toBe('Attachment');

    $log = ActivityLog::where('module', 'ATTACHMENT')->where('action', 'CREATE')->first();
    expect($log)->not->toBeNull();
    expect($log->record_id)->toBe((string) $trial->id);
});

test('an invalid category is rejected and nothing is persisted', function () {
    Storage::fake('legacy_uploads');

    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email]);
    makeAttachmentCategory('Vacuum Test');

    $response = $this->actingAs($owner)->post(route('trials.attachments.store', $trial), [
        'category' => 'Not A Real Category',
        'photos' => [UploadedFile::fake()->image('photo1.jpg')],
    ]);

    $response->assertSessionHasErrors('category');
    expect(TrialAttachmentFile::where('trial_id', $trial->id)->count())->toBe(0);
});

test('no photos submitted is rejected', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email]);
    makeAttachmentCategory('Vacuum Test');

    $response = $this->actingAs($owner)->post(route('trials.attachments.store', $trial), [
        'category' => 'Vacuum Test',
    ]);

    $response->assertSessionHasErrors('photos');
});

test('a non-image file is skipped with an error while valid files in the same batch still save', function () {
    Storage::fake('legacy_uploads');

    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email]);
    makeAttachmentCategory('Vacuum Test');

    $response = $this->actingAs($owner)->post(route('trials.attachments.store', $trial), [
        'category' => 'Vacuum Test',
        'photos' => [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect(route('trials.attachments.edit', $trial));
    expect(TrialAttachmentFile::where('trial_id', $trial->id)->count())->toBe(1);
});

test('a file over 10MB is rejected while the rest of the batch still saves', function () {
    Storage::fake('legacy_uploads');

    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email]);
    makeAttachmentCategory('Vacuum Test');

    $this->actingAs($owner)->post(route('trials.attachments.store', $trial), [
        'category' => 'Vacuum Test',
        'photos' => [
            UploadedFile::fake()->image('too-big.jpg')->size(10 * 1024 + 1),
            UploadedFile::fake()->image('ok.jpg'),
        ],
    ]);

    expect(TrialAttachmentFile::where('trial_id', $trial->id)->count())->toBe(1);
});

test('deleting an attachment hard-deletes the row, the file, and writes an activity log', function () {
    Storage::fake('legacy_uploads');

    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email]);
    Storage::disk('legacy_uploads')->put($trial->id.'/photo.jpg', 'fake-contents');
    $file = TrialAttachmentFile::create([
        'trial_id' => $trial->id,
        'category' => 'Vacuum Test',
        'file_name' => 'photo.jpg',
        'file_path' => "/uploads/{$trial->id}/photo.jpg",
        'uploaded_by' => $owner->email,
    ]);

    $response = $this->actingAs($owner)->delete(route('trials.attachments.destroy', [$trial, $file]));

    $response->assertRedirect(route('trials.attachments.edit', $trial));
    expect(TrialAttachmentFile::find($file->id))->toBeNull();
    Storage::disk('legacy_uploads')->assertMissing($trial->id.'/photo.jpg');

    $log = ActivityLog::where('module', 'ATTACHMENT')->where('action', 'DELETE')->first();
    expect($log)->not->toBeNull();
    expect($log->record_id)->toBe((string) $file->id);
});

test('a staff member without edit rights is forbidden from uploading or deleting', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $otherStaff = User::factory()->create(['email' => 'other@local.test']);
    $trial = makeAttachmentTrial(['created_by' => $owner->email]);
    makeAttachmentCategory('Vacuum Test');

    $this->actingAs($otherStaff)->post(route('trials.attachments.store', $trial), [
        'category' => 'Vacuum Test',
        'photos' => [UploadedFile::fake()->image('photo1.jpg')],
    ])->assertForbidden();

    $file = TrialAttachmentFile::create([
        'trial_id' => $trial->id,
        'category' => 'Vacuum Test',
        'file_name' => 'photo.jpg',
        'file_path' => "/uploads/{$trial->id}/photo.jpg",
        'uploaded_by' => $owner->email,
    ]);

    $this->actingAs($otherStaff)->delete(route('trials.attachments.destroy', [$trial, $file]))->assertForbidden();
});
