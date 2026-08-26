<?php

use App\Models\ActivityLog;
use App\Models\User;

function makeActivityLog(array $attributes = []): ActivityLog
{
    return ActivityLog::create(array_merge([
        'user_name' => 'Staff Trial',
        'user_role' => 'Staff',
        'action' => 'CREATE',
        'module' => 'TRIAL',
        'record_label' => 'TRIAL-'.uniqid(),
        'old_data' => null,
        'new_data' => '{"foo":"bar"}',
        'ip_address' => '127.0.0.1',
    ], $attributes));
}

test('non-admin cannot view the activity logs list', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $this->actingAs($staff)
        ->get(route('admin.activity-logs.index'))
        ->assertForbidden();
});

test('admin can view the activity logs list', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeActivityLog(['record_label' => 'Findable Record']);

    $response = $this->actingAs($admin)->get(route('admin.activity-logs.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('logs.data', fn ($data) => collect($data)->pluck('record_label')->contains('Findable Record')));
});

test('super admin can view the activity logs list', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);

    $this->actingAs($superAdmin)
        ->get(route('admin.activity-logs.index'))
        ->assertOk();
});

test('the list can be filtered by module', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeActivityLog(['module' => 'TRIAL', 'record_label' => 'Trial Record']);
    makeActivityLog(['module' => 'PRODUCT', 'record_label' => 'Product Record']);

    $response = $this->actingAs($admin)->get(route('admin.activity-logs.index', ['module' => 'PRODUCT']));

    $response->assertInertia(fn ($page) => $page
        ->where('logs.data', fn ($data) => collect($data)->pluck('record_label')->all() === ['Product Record']));
});

test('the list can be filtered by search keyword against record_label', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    makeActivityLog(['record_label' => 'Unique Needle Record']);
    makeActivityLog(['record_label' => 'Unrelated Record']);

    $response = $this->actingAs($admin)->get(route('admin.activity-logs.index', ['q' => 'Needle']));

    $response->assertInertia(fn ($page) => $page
        ->where('logs.data', fn ($data) => collect($data)->pluck('record_label')->all() === ['Unique Needle Record']));
});

test('admin can permanently delete a single activity log', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $log = makeActivityLog();

    $this->actingAs($admin)
        ->delete(route('admin.activity-logs.destroy', $log))
        ->assertRedirect(route('admin.activity-logs.index'));

    expect(ActivityLog::find($log->id))->toBeNull();
});

test('staff cannot delete an activity log', function () {
    $staff = User::factory()->create(['role' => 'Staff']);
    $log = makeActivityLog();

    $this->actingAs($staff)
        ->delete(route('admin.activity-logs.destroy', $log))
        ->assertForbidden();

    expect(ActivityLog::find($log->id))->not->toBeNull();
});

test('admin can bulk delete selected activity logs', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $keep = makeActivityLog();
    $deleteA = makeActivityLog();
    $deleteB = makeActivityLog();

    $this->actingAs($admin)
        ->post(route('admin.activity-logs.destroy-selected'), [
            'log_ids' => [$deleteA->id, $deleteB->id],
        ])
        ->assertRedirect(route('admin.activity-logs.index'));

    expect(ActivityLog::find($keep->id))->not->toBeNull();
    expect(ActivityLog::find($deleteA->id))->toBeNull();
    expect(ActivityLog::find($deleteB->id))->toBeNull();
});

test('staff cannot bulk delete activity logs', function () {
    $staff = User::factory()->create(['role' => 'Staff']);
    $log = makeActivityLog();

    $this->actingAs($staff)
        ->post(route('admin.activity-logs.destroy-selected'), [
            'log_ids' => [$log->id],
        ])
        ->assertForbidden();

    expect(ActivityLog::find($log->id))->not->toBeNull();
});
