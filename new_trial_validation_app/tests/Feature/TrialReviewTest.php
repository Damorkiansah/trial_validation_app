<?php

use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Trial;
use App\Models\TrialReview;
use App\Models\User;
use App\Models\ValidationParameter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

function makeReviewableTrial(array $attributes = []): Trial
{
    return Trial::create([
        'trial_code' => $attributes['trial_code'] ?? 'TRIAL-REVIEW-1',
        'product_name' => 'Sample Product',
        'product_type' => $attributes['product_type'] ?? 'Tube',
        'progress_status' => $attributes['progress_status'] ?? 'Draft',
        'current_step' => $attributes['current_step'] ?? 'Attachment',
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

function makeCompleteTrial(array $attributes = []): Trial
{
    $trial = makeReviewableTrial($attributes);
    $param = ValidationParameter::create([
        'product_type' => $trial->product_type,
        'parameter_name' => 'Weight',
        'specification' => 'Spec',
        'sort_order' => 1,
    ]);

    DB::table('trials_results')->insert([
        'trial_id' => $trial->id,
        'parameter_id' => $param->id,
        'result_value' => 'Conform',
        'decision' => 'OK',
        'remark' => '',
        'updated_at' => Carbon::now(),
    ]);

    return $trial;
}

test('the review page shows completeness errors when validation is incomplete', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeReviewableTrial(['created_by' => $owner->email]);
    ValidationParameter::create([
        'product_type' => $trial->product_type,
        'parameter_name' => 'Weight',
        'specification' => 'Spec',
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($owner)->get(route('trials.review.edit', $trial));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('canEdit', true)
        ->where('completeness.0', 'Parameter Weight belum memiliki decision.'));
});

test('the review page shows no completeness errors when validation is complete', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeCompleteTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->get(route('trials.review.edit', $trial));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('completeness', []));
});

test('a soft-deleted trial 404s on the review page', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeCompleteTrial(['created_by' => $owner->email]);
    $trial->deleted_at = Carbon::now();
    $trial->save();

    $this->actingAs($owner)->get(route('trials.review.edit', $trial))->assertNotFound();
});

test('submitting for review creates pending trials_review rows and moves the trial to In Review', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $approver = User::factory()->create(['role' => 'Manager QAC']);
    $trial = makeCompleteTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->post(route('trials.review.store', $trial), [
        'departments' => ['PRD', 'QAC'],
        'approver_user_id' => $approver->id,
    ]);

    $response->assertRedirect(route('trials.review.edit', $trial));

    $reviews = TrialReview::where('trial_id', $trial->id)->orderBy('department')->get();
    expect($reviews)->toHaveCount(2);
    expect($reviews->pluck('department')->all())->toBe(['PRD', 'QAC']);
    expect($reviews->every(fn (TrialReview $r) => $r->status === 'Pending'))->toBeTrue();

    $trial->refresh();
    expect($trial->progress_status)->toBe('In Review');
    expect($trial->current_step)->toBe('Review');
    expect($trial->pending_with)->toBe('PRD,QAC');
    expect($trial->approver_user_id)->toBe($approver->id);

    $log = ActivityLog::where('module', 'REVIEW')->where('action', 'SUBMIT_REVIEW')->first();
    expect($log)->not->toBeNull();
    expect($log->record_id)->toBe((string) $trial->id);

    expect(Notification::where('trial_id', $trial->id)->where('type', 'review')->count())->toBe(2);
    expect(Notification::where('trial_id', $trial->id)->where('type', 'info')->count())->toBe(1);
});

test('submitting for review is rejected when validation is incomplete and nothing is persisted', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $approver = User::factory()->create(['role' => 'Manager QAC']);
    $trial = makeReviewableTrial(['created_by' => $owner->email]);
    ValidationParameter::create([
        'product_type' => $trial->product_type,
        'parameter_name' => 'Weight',
        'specification' => 'Spec',
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($owner)->post(route('trials.review.store', $trial), [
        'departments' => ['PRD'],
        'approver_user_id' => $approver->id,
    ]);

    $response->assertSessionHasErrors('completeness');
    expect(TrialReview::where('trial_id', $trial->id)->count())->toBe(0);
    expect($trial->fresh()->progress_status)->toBe('Draft');
});

test('submitting for review with an unknown department is rejected', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $approver = User::factory()->create(['role' => 'Manager QAC']);
    $trial = makeCompleteTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->post(route('trials.review.store', $trial), [
        'departments' => ['BOGUS'],
        'approver_user_id' => $approver->id,
    ]);

    $response->assertSessionHasErrors('departments.0');
    expect(TrialReview::where('trial_id', $trial->id)->count())->toBe(0);
});

test('submitting for review with an inactive approver is rejected', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $approver = User::factory()->create(['role' => 'Manager QAC', 'is_active' => false]);
    $trial = makeCompleteTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->post(route('trials.review.store', $trial), [
        'departments' => ['PRD'],
        'approver_user_id' => $approver->id,
    ]);

    $response->assertSessionHasErrors('approver_user_id');
});

test('a staff member without edit rights is forbidden from submitting for review', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $otherStaff = User::factory()->create(['email' => 'other@local.test']);
    $approver = User::factory()->create(['role' => 'Manager QAC']);
    $trial = makeCompleteTrial(['created_by' => $owner->email]);

    $this->actingAs($otherStaff)->post(route('trials.review.store', $trial), [
        'departments' => ['PRD'],
        'approver_user_id' => $approver->id,
    ])->assertForbidden();
});
