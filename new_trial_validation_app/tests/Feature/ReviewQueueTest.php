<?php

use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Trial;
use App\Models\TrialReview;
use App\Models\User;

function makeInReviewTrial(array $attributes = []): Trial
{
    return Trial::create([
        'trial_code' => $attributes['trial_code'] ?? 'TRIAL-INREVIEW-1',
        'product_name' => 'Sample Product',
        'product_type' => 'Tube',
        'progress_status' => 'In Review',
        'current_step' => 'Review',
        'created_by' => $attributes['created_by'] ?? 'owner@local.test',
        'revision_no' => 0,
        'pending_with' => $attributes['pending_with'] ?? 'PRD',
        'approver_user_id' => $attributes['approver_user_id'] ?? null,
    ]);
}

test('a reviewer only sees pending reviews for their own department', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeInReviewTrial();
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Pending']);
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'QAC', 'review_round' => 1, 'status' => 'Pending']);

    $otherTrial = makeInReviewTrial(['trial_code' => 'TRIAL-INREVIEW-2']);
    TrialReview::create(['trial_id' => $otherTrial->id, 'department' => 'RNI', 'review_round' => 1, 'status' => 'Pending']);

    $response = $this->actingAs($reviewer)->get(route('reviews.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('items.data', 1)
        ->where('items.data.0.trial_id', $trial->id));
});

test('a non-reviewer is forbidden from the review queue', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)->get(route('reviews.index'))->assertForbidden();
});

test('submitting a department review marks it reviewed and keeps the trial In Review when other departments are pending', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeInReviewTrial(['pending_with' => 'PRD,QAC']);
    $review = TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Pending']);
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'QAC', 'review_round' => 1, 'status' => 'Pending']);

    $response = $this->actingAs($reviewer)->put(route('reviews.update', $review), [
        'comment' => 'Looks good',
    ]);

    $response->assertRedirect(route('reviews.index'));

    $review->refresh();
    expect($review->status)->toBe('Reviewed');
    expect($review->comment)->toBe('Looks good');
    expect($review->reviewer_email)->toBe($reviewer->email);

    $trial->refresh();
    expect($trial->progress_status)->toBe('In Review');
    expect($trial->pending_with)->toBe('QAC');

    $log = ActivityLog::where('module', 'REVIEW')->where('record_id', (string) $review->id)->first();
    expect($log)->not->toBeNull();
});

test('submitting the last pending department review promotes the trial to Ready for Approval', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $approver = User::factory()->create(['name' => 'Approver One', 'role' => 'Manager QAC']);
    $trial = makeInReviewTrial(['pending_with' => 'PRD', 'approver_user_id' => $approver->id]);
    $review = TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Pending']);

    $this->actingAs($reviewer)->put(route('reviews.update', $review), [
        'comment' => 'All good',
    ]);

    $trial->refresh();
    expect($trial->progress_status)->toBe('Ready for Approval');
    expect($trial->current_step)->toBe('Approval');
    expect($trial->pending_with)->toBe('Approver One');

    expect(Notification::where('trial_id', $trial->id)->where('type', 'approval')->count())->toBe(2);
});

test('a reviewer from a different department is forbidden from saving a review that is not theirs', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeInReviewTrial();
    $review = TrialReview::create(['trial_id' => $trial->id, 'department' => 'QAC', 'review_round' => 1, 'status' => 'Pending']);

    $this->actingAs($reviewer)->put(route('reviews.update', $review), [
        'comment' => 'Trying to review anyway',
    ])->assertForbidden();
});

test('submitting a review without a comment is rejected', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeInReviewTrial();
    $review = TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Pending']);

    $this->actingAs($reviewer)->put(route('reviews.update', $review), [
        'comment' => '',
    ])->assertSessionHasErrors('comment');

    expect($review->fresh()->status)->toBe('Pending');
});

test('a review that has already been reviewed cannot be saved again', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeInReviewTrial();
    $review = TrialReview::create([
        'trial_id' => $trial->id,
        'department' => 'PRD',
        'review_round' => 1,
        'status' => 'Reviewed',
        'reviewer_name' => 'Someone Else',
        'comment' => 'Already done',
    ]);

    $this->actingAs($reviewer)->put(route('reviews.update', $review), [
        'comment' => 'Trying again',
    ])->assertForbidden();
});

test('a stale review from an older round cannot be saved', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeInReviewTrial(['trial_code' => 'TRIAL-STALE-1']);
    $trial->revision_no = 1;
    $trial->save();
    $review = TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Pending']);

    $this->actingAs($reviewer)->put(route('reviews.update', $review), [
        'comment' => 'Too late',
    ])->assertForbidden();
});
