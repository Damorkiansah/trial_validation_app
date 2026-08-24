<?php

use App\Models\User;

function makeUser(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

test('non-admin cannot view the users list', function () {
    $user = makeUser(['role' => 'Staff']);

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admin can view the users list', function () {
    $admin = makeUser(['role' => 'Admin']);
    makeUser(['name' => 'Searchable Person', 'role' => 'Staff']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk();
});

test('admin can create a user', function () {
    $admin = makeUser(['role' => 'Admin']);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'New Person',
        'email' => 'new.person@example.com',
        'password' => 'password123',
        'role' => 'Staff',
        'department' => '',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $created = User::where('email', 'new.person@example.com')->first();
    expect($created)->not->toBeNull();
    expect($created->role)->toBe('Staff');
    expect($created->is_active)->toBeTrue();
});

test('saving with an existing email updates that user instead of creating a new one', function () {
    $admin = makeUser(['role' => 'Admin']);
    $existing = makeUser(['email' => 'existing@example.com', 'role' => 'Staff', 'name' => 'Old Name']);

    $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Updated Name',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'role' => 'Viewer',
        'department' => '',
    ])->assertRedirect(route('admin.users.index'));

    expect(User::count())->toBe(2);
    $existing->refresh();
    expect($existing->name)->toBe('Updated Name');
    expect($existing->role)->toBe('Viewer');
});

test('non-super-admin cannot grant the super admin role when one already exists', function () {
    $admin = makeUser(['role' => 'Admin']);
    makeUser(['role' => 'Super Admin']);

    $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Wannabe',
        'email' => 'wannabe@example.com',
        'password' => 'password123',
        'role' => 'Super Admin',
        'department' => '',
    ])->assertSessionHasErrors('role');

    expect(User::where('email', 'wannabe@example.com')->exists())->toBeFalse();
});

test('non-super-admin cannot edit an existing super admin account', function () {
    $admin = makeUser(['role' => 'Admin']);
    $superAdmin = makeUser(['role' => 'Super Admin', 'email' => 'boss@example.com']);

    $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Hijacked',
        'email' => 'boss@example.com',
        'password' => 'password123',
        'role' => 'Staff',
        'department' => '',
    ])->assertForbidden();

    $superAdmin->refresh();
    expect($superAdmin->name)->not->toBe('Hijacked');
});

test('admin can soft delete another user', function () {
    $admin = makeUser(['role' => 'Admin']);
    $target = makeUser(['role' => 'Staff']);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $target))
        ->assertRedirect(route('admin.users.index'));

    $target->refresh();
    expect($target->is_active)->toBeFalse();
    expect($target->deleted_at)->not->toBeNull();
    expect($target->deleted_by)->toBe($admin->id);
});

test('admin cannot delete their own account from this screen', function () {
    $admin = makeUser(['role' => 'Admin']);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertForbidden();
});

test('non-super-admin cannot delete a super admin account', function () {
    $admin = makeUser(['role' => 'Admin']);
    $superAdmin = makeUser(['role' => 'Super Admin']);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $superAdmin))
        ->assertForbidden();

    expect($superAdmin->fresh()->is_active)->toBeTrue();
});
