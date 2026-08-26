<?php

use App\Models\MasterOption;
use App\Models\User;

function makeMasterOption(array $attributes = []): MasterOption
{
    return MasterOption::create(array_merge([
        'type' => 'machine_used',
        'name' => 'Sample Machine '.uniqid(),
        'sort_order' => 0,
        'is_active' => true,
    ], $attributes));
}

test('non-admin/staff cannot view the masters list', function () {
    $viewer = User::factory()->create(['role' => 'Viewer']);

    $this->actingAs($viewer)
        ->get(route('admin.masters.index'))
        ->assertForbidden();
});

test('admin can view the masters list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeMasterOption(['name' => 'Searchable Machine']);

    $this->actingAs($admin)
        ->get(route('admin.masters.index'))
        ->assertOk();
});

test('staff can view the masters list', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $this->actingAs($staff)
        ->get(route('admin.masters.index'))
        ->assertOk();
});

test('admin can create a master option', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $response = $this->actingAs($admin)->post(route('admin.masters.store'), [
        'type' => 'validation_category',
        'name' => 'New Category',
        'sort_order' => 2,
    ]);

    $response->assertRedirect(route('admin.masters.index'));

    $created = MasterOption::where('name', 'New Category')->first();
    expect($created)->not->toBeNull();
    expect($created->type)->toBe('validation_category');
    expect($created->sort_order)->toBe(2);
    expect($created->is_active)->toBeTrue();
});

test('saving by id updates that master option instead of creating a new one', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $existing = makeMasterOption(['type' => 'machine_used', 'name' => 'Old Name']);

    $this->actingAs($admin)->post(route('admin.masters.store'), [
        'id' => $existing->id,
        'type' => 'validation_scope',
        'name' => 'Updated Name',
        'sort_order' => 5,
    ])->assertRedirect(route('admin.masters.index'));

    expect(MasterOption::count())->toBe(1);
    $existing->refresh();
    expect($existing->type)->toBe('validation_scope');
    expect($existing->name)->toBe('Updated Name');
    expect($existing->sort_order)->toBe(5);
});

test('saving without an id upserts by type and name', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $existing = makeMasterOption(['type' => 'machine_used', 'name' => 'Mixer A', 'sort_order' => 1]);

    $this->actingAs($admin)->post(route('admin.masters.store'), [
        'type' => 'machine_used',
        'name' => 'Mixer A',
        'sort_order' => 9,
    ])->assertRedirect(route('admin.masters.index'));

    expect(MasterOption::count())->toBe(1);
    $existing->refresh();
    expect($existing->sort_order)->toBe(9);
});

test('staff cannot access masters at all when not admin or staff', function () {
    $reviewer = User::factory()->create(['role' => 'QC']);

    $this->actingAs($reviewer)->post(route('admin.masters.store'), [
        'type' => 'machine_used',
        'name' => 'Blocked Machine',
    ])->assertForbidden();

    expect(MasterOption::where('name', 'Blocked Machine')->exists())->toBeFalse();
});

test('creating a master option without a valid type or name fails validation', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin)->post(route('admin.masters.store'), [
        'type' => '',
        'name' => '',
    ])->assertSessionHasErrors(['type', 'name']);
});

test('admin cannot use a privileged type through the masters form', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin)->post(route('admin.masters.store'), [
        'type' => 'role_category',
        'name' => 'Sneaky Role',
    ])->assertSessionHasErrors(['type']);

    expect(MasterOption::where('name', 'Sneaky Role')->exists())->toBeFalse();
});

test('super admin can use a privileged type through the masters form', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $this->actingAs($superAdmin)->post(route('admin.masters.store'), [
        'type' => 'reviewer_department',
        'name' => 'QAC',
    ])->assertRedirect(route('admin.masters.index'));

    expect(MasterOption::where('type', 'reviewer_department')->where('name', 'QAC')->exists())->toBeTrue();
});

test('super admin cannot use a privileged type name longer than 50 characters', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $this->actingAs($superAdmin)->post(route('admin.masters.store'), [
        'type' => 'reviewer_department',
        'name' => str_repeat('a', 51),
    ])->assertSessionHasErrors(['name']);
});

test('admin can soft delete a master option', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $option = makeMasterOption();

    $this->actingAs($admin)
        ->delete(route('admin.masters.destroy', $option))
        ->assertRedirect(route('admin.masters.index'));

    $option->refresh();
    expect($option->is_active)->toBeFalse();
    expect($option->deleted_at)->not->toBeNull();
    expect($option->deleted_by)->toBe($admin->id);
});

test('deleted master options do not appear in the list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $option = makeMasterOption(['name' => 'To Be Deleted']);

    $this->actingAs($admin)->delete(route('admin.masters.destroy', $option));

    $response = $this->actingAs($admin)->get(route('admin.masters.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('options.data', fn ($data) => collect($data)->pluck('name')->doesntContain('To Be Deleted')));
});

test('admin cannot delete a privileged type master option', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $option = makeMasterOption(['type' => 'reviewer_department', 'name' => 'QAC']);

    $this->actingAs($admin)
        ->delete(route('admin.masters.destroy', $option))
        ->assertSessionHasErrors(['name']);

    $option->refresh();
    expect($option->is_active)->toBeTrue();
});

test('super admin can delete a privileged type master option', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $option = makeMasterOption(['type' => 'reviewer_department', 'name' => 'QAC']);

    $this->actingAs($superAdmin)
        ->delete(route('admin.masters.destroy', $option))
        ->assertRedirect(route('admin.masters.index'));

    $option->refresh();
    expect($option->is_active)->toBeFalse();
});

test('privileged type master options are hidden from non-super-admins in the list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeMasterOption(['type' => 'reviewer_department', 'name' => 'QAC']);
    makeMasterOption(['type' => 'machine_used', 'name' => 'Visible Machine']);

    $response = $this->actingAs($admin)->get(route('admin.masters.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('options.data', fn ($data) => collect($data)->pluck('name')->contains('Visible Machine')
            && ! collect($data)->pluck('name')->contains('QAC'))
    );
});

test('privileged type master options are visible to super admins in the list', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    makeMasterOption(['type' => 'reviewer_department', 'name' => 'QAC']);

    $response = $this->actingAs($superAdmin)->get(route('admin.masters.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('options.data', fn ($data) => collect($data)->pluck('name')->contains('QAC')));
});
