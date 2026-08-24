<?php

use App\Models\MasterOption;
use App\Models\Trial;
use App\Models\TrialEditPermission;
use App\Models\User;

function makeDraftTrial(array $attributes = []): Trial
{
    return Trial::create(array_merge([
        'trial_code' => 'TRIAL-'.uniqid(),
        'product_name' => 'Sample Product',
        'finish_good_code' => 'FG-001',
        'product_type' => 'Tube',
        'progress_status' => 'Draft',
        'revision_no' => 0,
        'created_by' => 'owner@local.test',
    ], $attributes));
}

test('non-super-admin cannot view access rights', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin)
        ->get(route('admin.access-rights.index'))
        ->assertForbidden();
});

test('super admin can view access rights', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $this->actingAs($superAdmin)
        ->get(route('admin.access-rights.index'))
        ->assertOk();
});

test('super admin cannot change their own role from this screen', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $response = $this->actingAs($superAdmin)->post(route('admin.access-rights.users.role', $superAdmin), [
        'role' => 'Admin',
        'department' => '',
    ]);

    $response->assertSessionHasErrors('role');
    expect($superAdmin->refresh()->role)->toBe('Super Admin');
});

test('super admin can reassign another user role and department', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $target = User::factory()->create(['role' => 'Viewer', 'department' => null]);

    $response = $this->actingAs($superAdmin)->post(route('admin.access-rights.users.role', $target), [
        'role' => 'Staff',
        'department' => 'ops',
    ]);

    $response->assertRedirect(route('admin.access-rights.index'));
    $target->refresh();
    expect($target->role)->toBe('Staff');
    expect($target->department)->toBe('OPS');
});

test('assigning a reviewer department role auto-derives the department', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $target = User::factory()->create(['role' => 'Viewer']);

    $this->actingAs($superAdmin)->post(route('admin.access-rights.users.role', $target), [
        'role' => 'PRD',
        'department' => '',
    ]);

    $target->refresh();
    expect($target->role)->toBe('PRD');
    expect($target->department)->toBe('PRD');
});

test('reassigning to an unknown role is rejected', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $target = User::factory()->create(['role' => 'Viewer']);

    $response = $this->actingAs($superAdmin)->post(route('admin.access-rights.users.role', $target), [
        'role' => 'Not A Real Role',
        'department' => '',
    ]);

    $response->assertSessionHasErrors('role');
    expect($target->refresh()->role)->toBe('Viewer');
});

test('admin cannot reassign roles', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $target = User::factory()->create(['role' => 'Viewer']);

    $this->actingAs($admin)->post(route('admin.access-rights.users.role', $target), [
        'role' => 'Staff',
    ])->assertForbidden();

    expect($target->refresh()->role)->toBe('Viewer');
});

test('super admin can add a reviewer department', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $this->actingAs($superAdmin)->post(route('admin.access-rights.reviewer-departments.store'), [
        'name' => 'new dept',
        'sort_order' => 3,
    ])->assertRedirect(route('admin.access-rights.index'));

    $option = MasterOption::where('type', 'reviewer_department')->where('name', 'NEW DEPT')->first();
    expect($option)->not->toBeNull();
    expect($option->sort_order)->toBe(3);
    expect($option->is_active)->toBeTrue();
});

test('saving a reviewer department with the same name updates it instead of duplicating', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $existing = MasterOption::create(['type' => 'reviewer_department', 'name' => 'DEPT X', 'sort_order' => 1, 'is_active' => true]);

    $this->actingAs($superAdmin)->post(route('admin.access-rights.reviewer-departments.store'), [
        'name' => 'dept x',
        'sort_order' => 9,
    ])->assertRedirect(route('admin.access-rights.index'));

    expect(MasterOption::where('type', 'reviewer_department')->count())->toBe(1);
    expect($existing->refresh()->sort_order)->toBe(9);
});

test('super admin can soft delete and re-add a reviewer department', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $option = MasterOption::create(['type' => 'reviewer_department', 'name' => 'DEPT Y', 'sort_order' => 1, 'is_active' => true]);

    $this->actingAs($superAdmin)->delete(route('admin.access-rights.reviewer-departments.destroy', $option))
        ->assertRedirect(route('admin.access-rights.index'));

    $option->refresh();
    expect($option->is_active)->toBeFalse();
    expect($option->deleted_at)->not->toBeNull();

    $this->actingAs($superAdmin)->post(route('admin.access-rights.reviewer-departments.store'), [
        'name' => 'DEPT Y',
        'sort_order' => 5,
    ]);

    $option->refresh();
    expect(MasterOption::where('type', 'reviewer_department')->count())->toBe(1);
    expect($option->is_active)->toBeTrue();
    expect($option->deleted_at)->toBeNull();
    expect($option->sort_order)->toBe(5);
});

test('deleting a reviewer department requires it to actually be one', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $option = MasterOption::create(['type' => 'product_type', 'name' => 'Tube', 'sort_order' => 1, 'is_active' => true]);

    $this->actingAs($superAdmin)->delete(route('admin.access-rights.reviewer-departments.destroy', $option))
        ->assertNotFound();
});

test('super admin can grant draft edit permission to a staff user', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $staff = User::factory()->create(['role' => 'Staff', 'email' => 'staff@local.test']);
    $trial = makeDraftTrial(['created_by' => 'owner@local.test']);

    $this->actingAs($superAdmin)->post(route('admin.access-rights.draft-permissions.store'), [
        'trial_id' => $trial->id,
        'user_id' => $staff->id,
    ])->assertRedirect(route('admin.access-rights.index'));

    $permission = TrialEditPermission::where('trial_id', $trial->id)->where('user_id', $staff->id)->first();
    expect($permission)->not->toBeNull();
    expect($permission->can_edit)->toBeTrue();
    expect($permission->granted_by)->toBe($superAdmin->id);
    expect($permission->revoked_at)->toBeNull();
});

test('granting permission to the trial owner is rejected', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $staff = User::factory()->create(['role' => 'Staff', 'email' => 'owner@local.test']);
    $trial = makeDraftTrial(['created_by' => 'owner@local.test']);

    $response = $this->actingAs($superAdmin)->post(route('admin.access-rights.draft-permissions.store'), [
        'trial_id' => $trial->id,
        'user_id' => $staff->id,
    ]);

    $response->assertSessionHasErrors('user_id');
    expect(TrialEditPermission::count())->toBe(0);
});

test('granting permission for a non-draft trial or non-staff user is rejected', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $viewer = User::factory()->create(['role' => 'Viewer']);
    $approvedTrial = makeDraftTrial(['progress_status' => 'Approved']);
    $draftTrial = makeDraftTrial();

    $this->actingAs($superAdmin)->post(route('admin.access-rights.draft-permissions.store'), [
        'trial_id' => $approvedTrial->id,
        'user_id' => $viewer->id,
    ])->assertSessionHasErrors('trial_id');

    $this->actingAs($superAdmin)->post(route('admin.access-rights.draft-permissions.store'), [
        'trial_id' => $draftTrial->id,
        'user_id' => $viewer->id,
    ])->assertSessionHasErrors('trial_id');

    expect(TrialEditPermission::count())->toBe(0);
});

test('super admin can revoke a granted permission', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $staff = User::factory()->create(['role' => 'Staff']);
    $trial = makeDraftTrial();
    $permission = TrialEditPermission::create([
        'trial_id' => $trial->id,
        'user_id' => $staff->id,
        'can_edit' => true,
        'granted_by' => $superAdmin->id,
        'granted_at' => now(),
    ]);

    $this->actingAs($superAdmin)->post(route('admin.access-rights.draft-permissions.revoke', $permission))
        ->assertRedirect(route('admin.access-rights.index'));

    $permission->refresh();
    expect($permission->can_edit)->toBeFalse();
    expect($permission->revoked_by)->toBe($superAdmin->id);
    expect($permission->revoked_at)->not->toBeNull();
});

test('re-granting a revoked permission reactivates the same row', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $staff = User::factory()->create(['role' => 'Staff']);
    $trial = makeDraftTrial();
    $permission = TrialEditPermission::create([
        'trial_id' => $trial->id,
        'user_id' => $staff->id,
        'can_edit' => false,
        'granted_by' => $superAdmin->id,
        'granted_at' => now()->subDay(),
        'revoked_by' => $superAdmin->id,
        'revoked_at' => now(),
    ]);

    $this->actingAs($superAdmin)->post(route('admin.access-rights.draft-permissions.store'), [
        'trial_id' => $trial->id,
        'user_id' => $staff->id,
    ]);

    expect(TrialEditPermission::count())->toBe(1);
    $permission->refresh();
    expect($permission->can_edit)->toBeTrue();
    expect($permission->revoked_at)->toBeNull();
});
