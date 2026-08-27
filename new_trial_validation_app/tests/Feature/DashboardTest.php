<?php

use App\Models\Trial;
use App\Models\TrialReview;
use App\Models\User;

function makeDashboardTrial(array $attributes = []): Trial
{
    return Trial::create(array_merge([
        'trial_code' => 'TRIAL-'.uniqid(),
        'product_name' => 'Sample Product',
        'finish_good_code' => 'FG-001',
        'product_type' => 'Tube',
        'progress_status' => 'Approved',
        'revision_no' => 0,
        'created_by' => 'owner@local.test',
    ], $attributes));
}

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the dashboard summary counts match the visible trials', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    makeDashboardTrial(['trial_code' => 'TRIAL-DRAFT', 'progress_status' => 'Draft']);
    makeDashboardTrial(['trial_code' => 'TRIAL-APPROVED-1', 'progress_status' => 'Approved']);
    makeDashboardTrial(['trial_code' => 'TRIAL-APPROVED-2', 'progress_status' => 'Approved']);
    makeDashboardTrial(['trial_code' => 'TRIAL-REJECTED', 'progress_status' => 'Rejected']);

    $response = $this->actingAs($superAdmin)->get(route('dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->where('summary.total', 4)
        ->where('summary.draft', 1)
        ->where('summary.approved', 2)
        ->where('summary.rejected', 1));
});

test('the q filter searches trial code and product name', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    makeDashboardTrial(['trial_code' => 'TRIAL-MATCH', 'product_name' => 'Other Product']);
    makeDashboardTrial(['trial_code' => 'TRIAL-OTHER', 'product_name' => 'Different Product']);

    $response = $this->actingAs($superAdmin)->get(route('dashboard', ['q' => 'MATCH']));

    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->pluck('trial_code')->contains('TRIAL-MATCH')
            && ! collect($data)->pluck('trial_code')->contains('TRIAL-OTHER')));
});

test('the status filter narrows the trial list', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    makeDashboardTrial(['trial_code' => 'TRIAL-DRAFT-1', 'progress_status' => 'Draft']);
    makeDashboardTrial(['trial_code' => 'TRIAL-APPROVED-1', 'progress_status' => 'Approved']);

    $response = $this->actingAs($superAdmin)->get(route('dashboard', ['status' => 'Draft']));

    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->pluck('trial_code')->contains('TRIAL-DRAFT-1')
            && ! collect($data)->pluck('trial_code')->contains('TRIAL-APPROVED-1')));
});

test('a non-super-admin does not see another user\'s draft trial', function () {
    $staff = User::factory()->create(['role' => 'Staff', 'email' => 'staff@local.test']);
    makeDashboardTrial(['trial_code' => 'TRIAL-SOMEONE-ELSE-DRAFT', 'progress_status' => 'Draft', 'created_by' => 'other@local.test']);
    makeDashboardTrial(['trial_code' => 'TRIAL-OWN-DRAFT', 'progress_status' => 'Draft', 'created_by' => 'staff@local.test']);

    $response = $this->actingAs($staff)->get(route('dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->pluck('trial_code')->contains('TRIAL-OWN-DRAFT')
            && ! collect($data)->pluck('trial_code')->contains('TRIAL-SOMEONE-ELSE-DRAFT')));
});

test('myWork lists the current user\'s own active trials but excludes finished ones', function () {
    $staff = User::factory()->create(['email' => 'staff@local.test']);
    makeDashboardTrial(['trial_code' => 'TRIAL-MINE-DRAFT', 'progress_status' => 'Draft', 'created_by' => 'staff@local.test']);
    makeDashboardTrial(['trial_code' => 'TRIAL-MINE-APPROVED', 'progress_status' => 'Approved', 'created_by' => 'staff@local.test']);
    makeDashboardTrial(['trial_code' => 'TRIAL-SOMEONE-ELSE', 'progress_status' => 'Draft', 'created_by' => 'other@local.test']);

    $response = $this->actingAs($staff)->get(route('dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->where('myWork.myTrialsTotal', 1)
        ->has('myWork.myTrials', 1)
        ->where('myWork.myTrials.0.trial_code', 'TRIAL-MINE-DRAFT'));
});

test('myWork lists pending reviews for the current user\'s department only', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeDashboardTrial(['trial_code' => 'TRIAL-REVIEW-MINE', 'progress_status' => 'In Review', 'revision_no' => 0]);
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Pending']);
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'QAC', 'review_round' => 1, 'status' => 'Pending']);

    $response = $this->actingAs($reviewer)->get(route('dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->where('myWork.pendingReviewsTotal', 1)
        ->has('myWork.pendingReviews', 1)
        ->where('myWork.pendingReviews.0.trial_code', 'TRIAL-REVIEW-MINE')
        ->where('myWork.pendingReviews.0.department', 'PRD'));
});

test('myWork lists only approvals specifically assigned to the current user, not the whole approval queue', function () {
    $managerQac = User::factory()->role('Manager QAC')->create();
    makeDashboardTrial(['trial_code' => 'TRIAL-APPROVAL-MINE', 'progress_status' => 'Ready for Approval', 'approver_user_id' => $managerQac->id]);
    makeDashboardTrial(['trial_code' => 'TRIAL-APPROVAL-OTHER', 'progress_status' => 'Ready for Approval', 'approver_user_id' => null]);

    $response = $this->actingAs($managerQac)->get(route('dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->where('myWork.pendingApprovalsTotal', 1)
        ->has('myWork.pendingApprovals', 1)
        ->where('myWork.pendingApprovals.0.trial_code', 'TRIAL-APPROVAL-MINE'));
});
