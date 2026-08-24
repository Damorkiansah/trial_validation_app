<?php

use App\Models\Product;
use App\Models\User;

function makeProduct(array $attributes = []): Product
{
    return Product::create(array_merge([
        'product_name' => 'Sample Product '.uniqid(),
        'finish_good_code' => 'FG-'.uniqid(),
        'is_active' => true,
    ], $attributes));
}

test('non-admin/staff cannot view the products list', function () {
    $viewer = User::factory()->create(['role' => 'Viewer']);

    $this->actingAs($viewer)
        ->get(route('admin.products.index'))
        ->assertForbidden();
});

test('admin can view the products list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeProduct(['product_name' => 'Searchable Product']);

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk();
});

test('staff can view the products list', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $this->actingAs($staff)
        ->get(route('admin.products.index'))
        ->assertOk();
});

test('admin can create a product', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $response = $this->actingAs($admin)->post(route('admin.products.store'), [
        'product_name' => 'New Product',
        'finish_good_code' => 'FG-NEW-001',
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $created = Product::where('product_name', 'New Product')->first();
    expect($created)->not->toBeNull();
    expect($created->finish_good_code)->toBe('FG-NEW-001');
    expect($created->is_active)->toBeTrue();
});

test('saving with an existing product name updates that product instead of creating a new one', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $existing = makeProduct(['product_name' => 'Existing Product', 'finish_good_code' => 'FG-OLD-001']);

    $this->actingAs($admin)->post(route('admin.products.store'), [
        'product_name' => 'Existing Product',
        'finish_good_code' => 'FG-UPDATED-001',
    ])->assertRedirect(route('admin.products.index'));

    expect(Product::count())->toBe(1);
    $existing->refresh();
    expect($existing->finish_good_code)->toBe('FG-UPDATED-001');
});

test('saving by id updates that product', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $existing = makeProduct(['product_name' => 'Old Name', 'finish_good_code' => 'FG-OLD-002']);

    $this->actingAs($admin)->post(route('admin.products.store'), [
        'id' => $existing->id,
        'product_name' => 'Updated Name',
        'finish_good_code' => 'FG-UPDATED-002',
    ])->assertRedirect(route('admin.products.index'));

    $existing->refresh();
    expect($existing->product_name)->toBe('Updated Name');
    expect($existing->finish_good_code)->toBe('FG-UPDATED-002');
});

test('renaming a product to collide with another existing product name fails validation', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeProduct(['product_name' => 'Taken Name']);
    $target = makeProduct(['product_name' => 'Other Name']);

    $this->actingAs($admin)->post(route('admin.products.store'), [
        'id' => $target->id,
        'product_name' => 'Taken Name',
        'finish_good_code' => 'FG-X',
    ])->assertSessionHasErrors('product_name');

    $target->refresh();
    expect($target->product_name)->toBe('Other Name');
});

test('staff cannot access products at all when not admin or staff', function () {
    $reviewer = User::factory()->create(['role' => 'QC']);

    $this->actingAs($reviewer)->post(route('admin.products.store'), [
        'product_name' => 'Blocked Product',
        'finish_good_code' => 'FG-BLOCKED',
    ])->assertForbidden();

    expect(Product::where('product_name', 'Blocked Product')->exists())->toBeFalse();
});

test('admin can soft delete a product', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $product = makeProduct();

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    $product->refresh();
    expect($product->is_active)->toBeFalse();
    expect($product->deleted_at)->not->toBeNull();
    expect($product->deleted_by)->toBe($admin->id);
});

test('deleted products do not appear in the list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $product = makeProduct(['product_name' => 'To Be Deleted']);

    $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

    $response = $this->actingAs($admin)->get(route('admin.products.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('products.data', fn ($data) => collect($data)->pluck('product_name')->doesntContain('To Be Deleted')));
});
