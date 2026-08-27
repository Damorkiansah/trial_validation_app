<?php

use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Trial;
use App\Models\User;
use Illuminate\Support\Carbon;

function makeApprovableTrial(array $attributes = []): Trial
{
    return Trial::create([
        'trial_code' => $attributes['trial_code'] ?? 'TRIAL-APPROVAL-1',
        'product_name' => 'Sample Product',
        'product_type' => $attributes['product_type'] ?? 'Tube',
        'progress_status' => $attributes['progress_status'] ?? 'Ready for Approval',
        'current_step' => $attributes['current_step'] ?? 'Approval',
        'created_by' => $attributes['created_by'] ?? 'owner@local.test',
        'batch_number' => 'B1',
        'bulk_code' => 'BC1',
        'support_team' => 'QA',
        'initiated_person_team' => 'Someone',
        'reason' => 'Testing',
        'bom' => 'BOM text',
        'revision_no' => $attributes['revision_no'] ?? 0,
        'approver_user_id' => $attributes['approver_user_id'] ?? null,
    ]);
}

test('a non-approver is forbidden from viewing the approval queue', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)->get(route('approvals.index'))->assertForbidden();
});

test('Manager QAC sees every trial in the approval queue', function () {
    $manager = User::factory()->role('Manager QAC')->create();
    $other = User::factory()->create();
    makeApprovableTrial(['trial_code' => 'TRIAL-A', 'approver_user_id' => $other->id]);
    makeApprovableTrial(['trial_code' => 'TRIAL-B']);

    $response = $this->actingAs($manager)->get(route('approvals.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->has('items.data', 2));
});

test('an assigned approver only sees their own assigned trials in the queue', function () {
    $approver = User::factory()->role('Team Leader')->create();
    $other = User::factory()->create();
    $mine = makeApprovableTrial(['trial_code' => 'TRIAL-MINE', 'approver_user_id' => $approver->id]);
    makeApprovableTrial(['trial_code' => 'TRIAL-OTHER', 'approver_user_id' => $other->id]);

    // Team Leader canSeeAll in legacy — confirm both are visible via the "see all" path.
    $response = $this->actingAs($approver)->get(route('approvals.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->has('items.data', 2));

    // A plain approver role not in the canSeeAll list only sees their own.
    $approver->update(['role' => 'Staff']);
    $approver->refresh();
    $mine->update(['approver_user_id' => $approver->id]);

    $response = $this->actingAs($approver)->get(route('approvals.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('items.data', 1)
        ->where('items.data.0.trial_code', 'TRIAL-MINE'));
});

test('a Team Leader can see but not approve a trial assigned to someone else', function () {
    $teamLeader = User::factory()->role('Team Leader')->create();
    $other = User::factory()->create();
    $trial = makeApprovableTrial(['approver_user_id' => $other->id]);

    $response = $this->actingAs($teamLeader)->post(route('approvals.update', $trial), [
        'decision' => 'Approved',
        'approval_comment' => 'Looks fine',
        'signature_password' => 'password',
    ]);

    $response->assertForbidden();
    expect($trial->fresh()->progress_status)->toBe('Ready for Approval');
});

test('Manager QAC can approve any trial regardless of approver_user_id', function () {
    $manager = User::factory()->role('Manager QAC')->create();
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $other = User::factory()->create();
    $trial = makeApprovableTrial(['created_by' => $owner->email, 'approver_user_id' => $other->id]);

    $response = $this->actingAs($manager)->post(route('approvals.update', $trial), [
        'decision' => 'Approved',
        'approval_comment' => 'Looks fine',
        'signature_password' => 'password',
    ]);

    $response->assertRedirect(route('approvals.index'));

    $trial->refresh();
    expect($trial->progress_status)->toBe('Approved');
    expect($trial->final_decision)->toBe('Approved');
    expect($trial->pending_with)->toBe('');
    expect($trial->approved_by)->toBe($manager->name);
    expect($trial->approved_at)->not->toBeNull();
    expect($trial->approval_comment)->toBe('Looks fine');

    $log = ActivityLog::where('module', 'APPROVAL')->where('action', 'APPROVE')->first();
    expect($log)->not->toBeNull();
    expect($log->record_id)->toBe((string) $trial->id);

    expect(Notification::where('trial_id', $trial->id)->where('type', 'approved')->count())->toBe(2);
});

test('a decision with the wrong e-signature password is rejected and nothing changes', function () {
    $manager = User::factory()->role('Manager QAC')->create();
    $trial = makeApprovableTrial();

    $response = $this->actingAs($manager)->post(route('approvals.update', $trial), [
        'decision' => 'Approved',
        'approval_comment' => 'Looks fine',
        'signature_password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('signature_password');
    expect($trial->fresh()->progress_status)->toBe('Ready for Approval');
});

test('Need Revision sends the trial back to Staff and bumps revision_no', function () {
    $manager = User::factory()->role('Manager QAC')->create();
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeApprovableTrial(['created_by' => $owner->email, 'revision_no' => 0]);

    $response = $this->actingAs($manager)->post(route('approvals.update', $trial), [
        'decision' => 'Need Revision',
        'approval_comment' => 'Please fix the weighing data',
        'signature_password' => 'password',
    ]);

    $response->assertRedirect(route('approvals.index'));

    $trial->refresh();
    expect($trial->progress_status)->toBe('Need Revision');
    expect($trial->current_step)->toBe('Revision');
    expect($trial->final_decision)->toBe('Need Revision');
    expect($trial->pending_with)->toBe('Staff');
    expect($trial->revision_no)->toBe(1);
    expect($trial->rejected_by)->toBe($manager->name);

    expect(Notification::where('trial_id', $trial->id)->where('type', 'revision')->count())->toBe(2);
});

test('Rejected is a final state', function () {
    $manager = User::factory()->role('Manager QAC')->create();
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeApprovableTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($manager)->post(route('approvals.update', $trial), [
        'decision' => 'Rejected',
        'approval_comment' => 'Does not meet spec',
        'signature_password' => 'password',
    ]);

    $response->assertRedirect(route('approvals.index'));

    $trial->refresh();
    expect($trial->progress_status)->toBe('Rejected');
    expect($trial->current_step)->toBe('Closed');
    expect($trial->final_decision)->toBe('Rejected');
    expect($trial->pending_with)->toBe('');

    expect(Notification::where('trial_id', $trial->id)->where('type', 'rejected')->count())->toBe(2);
});

test('a decision on a trial that is not Ready for Approval is rejected', function () {
    $manager = User::factory()->role('Manager QAC')->create();
    $trial = makeApprovableTrial(['progress_status' => 'In Review']);

    $response = $this->actingAs($manager)->post(route('approvals.update', $trial), [
        'decision' => 'Approved',
        'approval_comment' => 'Looks fine',
        'signature_password' => 'password',
    ]);

    $response->assertSessionHasErrors('decision');
    expect($trial->fresh()->progress_status)->toBe('In Review');
});

test('a soft-deleted trial 404s on the approval decision route', function () {
    $manager = User::factory()->role('Manager QAC')->create();
    $trial = makeApprovableTrial();
    $trial->deleted_at = Carbon::now();
    $trial->save();

    $this->actingAs($manager)->post(route('approvals.update', $trial), [
        'decision' => 'Approved',
        'approval_comment' => 'Looks fine',
        'signature_password' => 'password',
    ])->assertNotFound();
});
