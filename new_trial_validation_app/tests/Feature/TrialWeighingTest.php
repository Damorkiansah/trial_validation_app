<?php

use App\Models\ActivityLog;
use App\Models\Trial;
use App\Models\TrialWeighing;
use App\Models\User;
use Illuminate\Support\Carbon;

function makeWeighingTrial(array $attributes = []): Trial
{
    return Trial::create([
        'trial_code' => $attributes['trial_code'] ?? 'TRIAL-WEIGH-1',
        'product_name' => 'Sample Product',
        'product_type' => $attributes['product_type'] ?? 'Tube',
        'progress_status' => $attributes['progress_status'] ?? 'Draft',
        'current_step' => $attributes['current_step'] ?? 'WeighingPackaging',
        'created_by' => $attributes['created_by'] ?? 'owner@local.test',
    ]);
}

test('a non-draft trial weighing page is viewable by a viewer with canEdit false', function () {
    $viewer = User::factory()->role('Viewer')->create();
    $trial = makeWeighingTrial(['progress_status' => 'In Review']);

    $response = $this->actingAs($viewer)->get(route('trials.weighing.edit', ['trial' => $trial, 'section' => 'Packaging']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('canEdit', false)->where('section', 'Packaging'));
});

test('an unknown section 404s', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $this->actingAs($owner)->get('/trials/'.$trial->id.'/weighing/Bogus')->assertNotFound();
});

test('a soft-deleted trial 404s on the weighing page', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);
    $trial->deleted_at = Carbon::now();
    $trial->save();

    $this->actingAs($owner)->get(route('trials.weighing.edit', ['trial' => $trial, 'section' => 'Packaging']))->assertNotFound();
});

test('saving packaging weighing samples persists rows, advances current_step, and redirects to filling', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'w' => ['1' => '10.5', '2' => '11.2', '3' => ''],
    ]);

    $response->assertRedirect(route('trials.weighing.edit', ['trial' => $trial, 'section' => 'Filling']));

    $rows = TrialWeighing::where('trial_id', $trial->id)->where('section', 'Packaging')->orderBy('item_no')->get();
    expect($rows)->toHaveCount(2);
    expect((float) $rows[0]->weight_value)->toBe(10.5);
    expect((float) $rows[1]->weight_value)->toBe(11.2);
    expect($rows->contains('is_skipped', true))->toBeFalse();

    expect($trial->fresh()->current_step)->toBe('WeighingFilling');
    expect($trial->fresh()->progress_status)->toBe('Draft');

    $log = ActivityLog::where('module', 'WEIGHING')->where('action', 'UPDATE')->first();
    expect($log)->not->toBeNull();
    expect($log->record_id)->toBe((string) $trial->id);
});

test('saving filling weighing samples advances current_step to attachment and redirects to attachments', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email, 'current_step' => 'WeighingFilling']);

    $response = $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Filling']), [
        'w' => ['1' => '30.0'],
    ]);

    $response->assertRedirect(route('trials.attachments.edit', $trial));
    expect($trial->fresh()->current_step)->toBe('Attachment');
});

test('checking skip stores a single sentinel row and ignores any w values', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'skip' => '1',
        'w' => ['1' => '10.5'],
    ]);

    $rows = TrialWeighing::where('trial_id', $trial->id)->where('section', 'Packaging')->get();
    expect($rows)->toHaveCount(1);
    expect($rows->first()->is_skipped)->toBeTrue();
    expect($rows->first()->weight_value)->toBeNull();
});

test('a non-numeric weighing value is rejected and nothing is persisted', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'w' => ['1' => 'not-a-number'],
    ]);

    $response->assertSessionHasErrors('w');
    expect(TrialWeighing::where('trial_id', $trial->id)->count())->toBe(0);
    expect($trial->fresh()->current_step)->toBe('WeighingPackaging');
});

test('a negative weighing value is rejected', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'w' => ['1' => '-5'],
    ]);

    $response->assertSessionHasErrors('w');
    expect(TrialWeighing::where('trial_id', $trial->id)->count())->toBe(0);
});

test('no samples and no skip is rejected', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'w' => ['1' => '', '2' => ''],
    ]);

    $response->assertSessionHasErrors('w');
    expect(TrialWeighing::where('trial_id', $trial->id)->count())->toBe(0);
});

test('re-saving weighing samples replaces existing rows wholesale', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'w' => ['1' => '10', '2' => '20', '3' => '30'],
    ]);

    $this->actingAs($owner)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'w' => ['1' => '99'],
    ]);

    $rows = TrialWeighing::where('trial_id', $trial->id)->where('section', 'Packaging')->get();
    expect($rows)->toHaveCount(1);
    expect((float) $rows->first()->weight_value)->toBe(99.0);
});

test('a staff member without edit rights is forbidden from saving weighing samples', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $otherStaff = User::factory()->create(['email' => 'other@local.test']);
    $trial = makeWeighingTrial(['created_by' => $owner->email]);

    $this->actingAs($otherStaff)->put(route('trials.weighing.update', ['trial' => $trial, 'section' => 'Packaging']), [
        'w' => ['1' => '10'],
    ])->assertForbidden();
});
