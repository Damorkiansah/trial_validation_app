<?php

use App\Models\Notification;
use App\Models\Trial;
use App\Models\User;

function makeNotification(array $attributes = []): Notification
{
    return Notification::create(array_merge([
        'title' => 'Sample Notification '.uniqid(),
        'message' => 'Something happened.',
        'type' => 'info',
    ], $attributes));
}

function makeTrialForNotification(array $attributes = []): Trial
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

test('non-admin cannot view the admin notifications list', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $this->actingAs($staff)
        ->get(route('admin.notifications.index'))
        ->assertForbidden();
});

test('admin can view the admin notifications list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeNotification(['title' => 'Trial approved']);

    $response = $this->actingAs($admin)->get(route('admin.notifications.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('notifications.data', fn ($data) => collect($data)->pluck('title')->contains('Trial approved')));
});

test('super admin can view the admin notifications list', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $this->actingAs($superAdmin)
        ->get(route('admin.notifications.index'))
        ->assertOk();
});

test('the list shows the target user and linked trial', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $target = User::factory()->create(['name' => 'Target User']);
    $trial = makeTrialForNotification();
    makeNotification(['user_id' => $target->id, 'trial_id' => $trial->id, 'title' => 'Linked notification']);

    $response = $this->actingAs($admin)->get(route('admin.notifications.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('notifications.data', fn ($data) => collect($data)
            ->firstWhere('title', 'Linked notification')['user']['name'] === 'Target User'
            && collect($data)->firstWhere('title', 'Linked notification')['trial']['trial_code'] === $trial->trial_code));
});

test('admin can permanently delete a notification', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $notification = makeNotification();

    $this->actingAs($admin)
        ->delete(route('admin.notifications.destroy', $notification))
        ->assertRedirect(route('admin.notifications.index'));

    expect(Notification::find($notification->id))->toBeNull();
});

test('staff cannot delete a notification', function () {
    $staff = User::factory()->create(['role' => 'Staff']);
    $notification = makeNotification();

    $this->actingAs($staff)
        ->delete(route('admin.notifications.destroy', $notification))
        ->assertForbidden();

    expect(Notification::find($notification->id))->not->toBeNull();
});
