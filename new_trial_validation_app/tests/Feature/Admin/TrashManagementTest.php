<?php

use App\Models\Trial;
use App\Models\User;
use Illuminate\Support\Carbon;

function makeTrashedTrial(array $attributes = [], ?User $deletedBy = null): Trial
{
    $trial = Trial::create(array_merge([
        'trial_code' => 'TRIAL-'.uniqid(),
        'product_name' => 'Sample Product',
        'finish_good_code' => 'FG-001',
        'product_type' => 'Tube',
        'progress_status' => 'Draft',
        'revision_no' => 0,
        'created_by' => 'owner@local.test',
    ], $attributes));

    $trial->deleted_at = Carbon::now();
    $trial->deleted_by = $deletedBy?->id;
    $trial->save();

    return $trial;
}

test('non-admin cannot view the trash list', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $this->actingAs($staff)
        ->get(route('admin.trash.index'))
        ->assertForbidden();
});

test('admin can view the trash list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeTrashedTrial(['trial_code' => 'TRIAL-DELETED-1']);

    $response = $this->actingAs($admin)->get(route('admin.trash.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->pluck('trial_code')->contains('TRIAL-DELETED-1')));
});

test('super admin can view the trash list', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $this->actingAs($superAdmin)
        ->get(route('admin.trash.index'))
        ->assertOk();
});

test('non-deleted trials are excluded from the trash list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    Trial::create([
        'trial_code' => 'TRIAL-ACTIVE',
        'product_name' => 'Active Product',
        'finish_good_code' => 'FG-002',
        'product_type' => 'Tube',
        'progress_status' => 'Draft',
        'revision_no' => 0,
        'created_by' => 'owner@local.test',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.trash.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => ! collect($data)->pluck('trial_code')->contains('TRIAL-ACTIVE')));
});

test('the list shows who created and who deleted the trial', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $creator = User::factory()->create(['name' => 'Creator User', 'email' => 'creator@local.test']);
    $deleter = User::factory()->create(['name' => 'Deleter User']);
    makeTrashedTrial(['trial_code' => 'TRIAL-LINKED', 'created_by' => $creator->email], $deleter);

    $response = $this->actingAs($admin)->get(route('admin.trash.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->firstWhere('trial_code', 'TRIAL-LINKED')['creator']['name'] === 'Creator User'
            && collect($data)->firstWhere('trial_code', 'TRIAL-LINKED')['deleted_by_user']['name'] === 'Deleter User'));
});

test('the q filter searches trial code, product name, and product type', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeTrashedTrial(['trial_code' => 'TRIAL-MATCH', 'product_name' => 'Other Product']);
    makeTrashedTrial(['trial_code' => 'TRIAL-OTHER', 'product_name' => 'Different Product']);

    $response = $this->actingAs($admin)->get(route('admin.trash.index', ['q' => 'MATCH']));

    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->pluck('trial_code')->contains('TRIAL-MATCH')
            && ! collect($data)->pluck('trial_code')->contains('TRIAL-OTHER')));
});

test('the deleted_by filter searches by the deleting user\'s name', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $deleter = User::factory()->create(['name' => 'Specific Deleter']);
    makeTrashedTrial(['trial_code' => 'TRIAL-BY-SPECIFIC'], $deleter);
    makeTrashedTrial(['trial_code' => 'TRIAL-BY-OTHER'], User::factory()->create(['name' => 'Someone Else']));

    $response = $this->actingAs($admin)->get(route('admin.trash.index', ['deleted_by' => 'Specific']));

    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->pluck('trial_code')->contains('TRIAL-BY-SPECIFIC')
            && ! collect($data)->pluck('trial_code')->contains('TRIAL-BY-OTHER')));
});

test('admin can restore a trashed trial', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $trial = makeTrashedTrial(['trial_code' => 'TRIAL-RESTORE-ME']);

    $this->actingAs($admin)
        ->post(route('admin.trash.restore', $trial))
        ->assertRedirect(route('admin.trash.index'));

    $trial->refresh();
    expect($trial->deleted_at)->toBeNull();
    expect($trial->deleted_by)->toBeNull();
});

test('restoring a trial that is not deleted does not error and leaves it untouched', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $trial = Trial::create([
        'trial_code' => 'TRIAL-NOT-DELETED',
        'product_name' => 'Active Product',
        'finish_good_code' => 'FG-003',
        'product_type' => 'Tube',
        'progress_status' => 'Draft',
        'revision_no' => 0,
        'created_by' => 'owner@local.test',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.trash.restore', $trial))
        ->assertRedirect(route('admin.trash.index'));

    $trial->refresh();
    expect($trial->deleted_at)->toBeNull();
});

test('staff cannot restore a trashed trial', function () {
    $staff = User::factory()->create(['role' => 'Staff']);
    $trial = makeTrashedTrial();

    $this->actingAs($staff)
        ->post(route('admin.trash.restore', $trial))
        ->assertForbidden();

    $trial->refresh();
    expect($trial->deleted_at)->not->toBeNull();
});
