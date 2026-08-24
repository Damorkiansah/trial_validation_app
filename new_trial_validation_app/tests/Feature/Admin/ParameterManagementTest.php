<?php

use App\Models\User;
use App\Models\ValidationParameter;

function makeParameter(array $attributes = []): ValidationParameter
{
    return ValidationParameter::create(array_merge([
        'product_type' => 'Tube',
        'parameter_name' => 'Sample Parameter '.uniqid(),
        'specification' => 'Sample spec',
        'sort_order' => 0,
        'is_active' => true,
    ], $attributes));
}

test('non-admin/staff cannot view the parameters list', function () {
    $viewer = User::factory()->create(['role' => 'Viewer']);

    $this->actingAs($viewer)
        ->get(route('admin.parameters.index'))
        ->assertForbidden();
});

test('admin can view the parameters list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeParameter(['parameter_name' => 'Searchable Parameter']);

    $this->actingAs($admin)
        ->get(route('admin.parameters.index'))
        ->assertOk();
});

test('staff can view the parameters list', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $this->actingAs($staff)
        ->get(route('admin.parameters.index'))
        ->assertOk();
});

test('admin can create a parameter', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $response = $this->actingAs($admin)->post(route('admin.parameters.store'), [
        'product_type' => 'Hotpouring',
        'parameter_name' => 'New Parameter',
        'specification' => 'Must be clean',
        'sort_order' => 2,
    ]);

    $response->assertRedirect(route('admin.parameters.index'));

    $created = ValidationParameter::where('parameter_name', 'New Parameter')->first();
    expect($created)->not->toBeNull();
    expect($created->product_type)->toBe('Hotpouring');
    expect($created->specification)->toBe('Must be clean');
    expect($created->sort_order)->toBe(2);
    expect($created->is_active)->toBeTrue();
});

test('saving by id updates that parameter instead of creating a new one', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $existing = makeParameter(['parameter_name' => 'Old Name', 'product_type' => 'Tube']);

    $this->actingAs($admin)->post(route('admin.parameters.store'), [
        'id' => $existing->id,
        'product_type' => 'Sachet',
        'parameter_name' => 'Updated Name',
        'specification' => 'Updated spec',
        'sort_order' => 5,
    ])->assertRedirect(route('admin.parameters.index'));

    expect(ValidationParameter::count())->toBe(1);
    $existing->refresh();
    expect($existing->product_type)->toBe('Sachet');
    expect($existing->parameter_name)->toBe('Updated Name');
    expect($existing->specification)->toBe('Updated spec');
    expect($existing->sort_order)->toBe(5);
});

test('saving without an id always creates a new parameter, even with a duplicate name', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeParameter(['parameter_name' => 'Duplicate Name']);

    $this->actingAs($admin)->post(route('admin.parameters.store'), [
        'product_type' => 'Mixing',
        'parameter_name' => 'Duplicate Name',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.parameters.index'));

    expect(ValidationParameter::where('parameter_name', 'Duplicate Name')->count())->toBe(2);
});

test('staff cannot access parameters at all when not admin or staff', function () {
    $reviewer = User::factory()->create(['role' => 'QC']);

    $this->actingAs($reviewer)->post(route('admin.parameters.store'), [
        'product_type' => 'Tube',
        'parameter_name' => 'Blocked Parameter',
    ])->assertForbidden();

    expect(ValidationParameter::where('parameter_name', 'Blocked Parameter')->exists())->toBeFalse();
});

test('creating a parameter without a product type or name fails validation', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin)->post(route('admin.parameters.store'), [
        'product_type' => '',
        'parameter_name' => '',
    ])->assertSessionHasErrors(['product_type', 'parameter_name']);
});

test('admin can soft delete a parameter', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $parameter = makeParameter();

    $this->actingAs($admin)
        ->delete(route('admin.parameters.destroy', $parameter))
        ->assertRedirect(route('admin.parameters.index'));

    $parameter->refresh();
    expect($parameter->is_active)->toBeFalse();
    expect($parameter->deleted_at)->not->toBeNull();
    expect($parameter->deleted_by)->toBe($admin->id);
});

test('deleted parameters do not appear in the list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $parameter = makeParameter(['parameter_name' => 'To Be Deleted']);

    $this->actingAs($admin)->delete(route('admin.parameters.destroy', $parameter));

    $response = $this->actingAs($admin)->get(route('admin.parameters.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('parameters.data', fn ($data) => collect($data)->pluck('parameter_name')->doesntContain('To Be Deleted')));
});
