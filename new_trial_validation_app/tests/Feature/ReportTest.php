<?php

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\Trial;
use App\Models\TrialReview;
use App\Models\User;
use App\Models\ValidationParameter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

function makeReportTrial(array $attributes = []): Trial
{
    return Trial::create([
        'trial_code' => $attributes['trial_code'] ?? 'TRIAL-REPORT-1',
        'product_name' => $attributes['product_name'] ?? 'Sample Product',
        'finish_good_code' => $attributes['finish_good_code'] ?? 'FG-1',
        'product_type' => $attributes['product_type'] ?? 'Tube',
        'validation_scope' => $attributes['validation_scope'] ?? ['Filling'],
        'machine_used' => $attributes['machine_used'] ?? ['Machine A'],
        'progress_status' => $attributes['progress_status'] ?? 'Approved',
        'final_decision' => $attributes['final_decision'] ?? null,
        'current_step' => $attributes['current_step'] ?? 'Closed',
        'created_by' => $attributes['created_by'] ?? 'owner@local.test',
        'batch_number' => 'B1',
        'bulk_code' => 'BC1',
        'support_team' => 'QA',
        'initiated_person_team' => 'Someone',
        'reason' => 'Testing',
        'bom' => 'BOM text',
        'revision_no' => $attributes['revision_no'] ?? 0,
        'approved_by' => $attributes['approved_by'] ?? null,
        'approved_at' => $attributes['approved_at'] ?? null,
        'rejected_by' => $attributes['rejected_by'] ?? null,
        'rejected_at' => $attributes['rejected_at'] ?? null,
        'approval_comment' => $attributes['approval_comment'] ?? null,
        'pending_with' => $attributes['pending_with'] ?? '',
        'approver_user_id' => $attributes['approver_user_id'] ?? null,
    ]);
}

test('the approved report only lists approved trials and resolves approved_by to a real name', function () {
    $owner = User::factory()->create();
    $manager = User::factory()->role('Manager QAC')->create(['email' => 'manager@local.test']);
    $approved = makeReportTrial([
        'trial_code' => 'TRIAL-APPROVED',
        'progress_status' => 'Approved',
        'approved_by' => 'manager@local.test',
        'approved_at' => Carbon::now(),
    ]);
    makeReportTrial(['trial_code' => 'TRIAL-INREVIEW', 'progress_status' => 'In Review']);

    $response = $this->actingAs($owner)->get(route('reports.approved'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('items.data', 1)
        ->where('items.data.0.id', $approved->id)
        ->where('items.data.0.approved_by', $manager->name));
});

test('the rejected report lists both Rejected status and Rejected final_decision trials', function () {
    $owner = User::factory()->create();
    makeReportTrial(['trial_code' => 'TRIAL-REJECTED-1', 'progress_status' => 'Rejected', 'rejected_by' => 'owner2@local.test']);
    makeReportTrial(['trial_code' => 'TRIAL-REJECTED-2', 'progress_status' => 'Need Revision', 'final_decision' => 'Rejected']);
    makeReportTrial(['trial_code' => 'TRIAL-APPROVED-1', 'progress_status' => 'Approved']);

    $response = $this->actingAs($owner)->get(route('reports.rejected'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->has('items.data', 2));
});

test('the trial summary report applies each filter field', function () {
    $owner = User::factory()->create();
    makeReportTrial([
        'trial_code' => 'TRIAL-SUMMARY-MATCH',
        'progress_status' => 'Approved',
        'product_type' => 'Tube',
        'product_name' => 'Special Cream',
        'validation_scope' => ['Filling', 'Capping'],
        'machine_used' => ['Autocaper'],
    ]);
    makeReportTrial([
        'trial_code' => 'TRIAL-SUMMARY-OTHER',
        'progress_status' => 'Ready for Approval',
        'product_type' => 'Bottle',
        'product_name' => 'Other Product',
        'validation_scope' => ['Coding'],
        'machine_used' => ['Wirapck'],
    ]);

    $cases = [
        ['status' => 'Approved'],
        ['product_type' => 'Tube'],
        ['validation_scope' => 'Capping'],
        ['machine_used' => 'Autocaper'],
        ['product_name' => 'Special'],
    ];

    foreach ($cases as $filters) {
        $response = $this->actingAs($owner)->get(route('reports.trial-summary', $filters));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('items.data', 1)
            ->where('items.data.0.trial_code', 'TRIAL-SUMMARY-MATCH'));
    }
});

test('the department review report shows a status per department and the correct overall review status', function () {
    $owner = User::factory()->create();
    $trial = makeReportTrial(['trial_code' => 'TRIAL-DEPTREVIEW', 'progress_status' => 'In Review', 'pending_with' => 'QAC']);
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Reviewed']);
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'QAC', 'review_round' => 1, 'status' => 'Pending']);

    $response = $this->actingAs($owner)->get(route('reports.department-review'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('items.data', 1)
        ->where('items.data.0.departments.PRD', 'Reviewed')
        ->where('items.data.0.departments.QAC', 'Pending')
        ->where('items.data.0.departments.RNI', 'N/A')
        ->where('items.data.0.review_status', 'Pending')
        ->where('items.data.0.pending_with', 'QAC'));
});

test('a reviewer only sees department review rows for trials tied to their own department', function () {
    $reviewer = User::factory()->role('PRD')->create();
    $trial = makeReportTrial(['trial_code' => 'TRIAL-DEPT-MINE', 'progress_status' => 'In Review']);
    TrialReview::create(['trial_id' => $trial->id, 'department' => 'PRD', 'review_round' => 1, 'status' => 'Pending']);

    $otherTrial = makeReportTrial(['trial_code' => 'TRIAL-DEPT-OTHER', 'progress_status' => 'In Review']);
    TrialReview::create(['trial_id' => $otherTrial->id, 'department' => 'RNI', 'review_round' => 1, 'status' => 'Pending']);

    $response = $this->actingAs($reviewer)->get(route('reports.department-review'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('items.data', 1)
        ->where('items.data.0.trial_code', 'TRIAL-DEPT-MINE'));
});

test('the audit print log lists report_printed entries newest first with the trial code resolved', function () {
    $owner = User::factory()->create();
    $trial = makeReportTrial(['trial_code' => 'TRIAL-AUDIT-1']);

    $older = AuditLog::create([
        'trial_id' => $trial->id,
        'user_email' => 'owner@local.test',
        'action' => 'report_printed',
        'old_data' => [],
        'new_data' => ['report_type' => 'Report Summary'],
    ]);
    $older->created_at = Carbon::now()->subMinute();
    $older->save();

    $newer = AuditLog::create([
        'trial_id' => $trial->id,
        'user_email' => 'owner@local.test',
        'action' => 'report_printed',
        'old_data' => [],
        'new_data' => ['report_type' => 'Report Summary'],
    ]);

    AuditLog::create([
        'trial_id' => $trial->id,
        'user_email' => 'owner@local.test',
        'action' => 'trial_header_updated',
        'old_data' => [],
        'new_data' => [],
    ]);

    $response = $this->actingAs($owner)->get(route('reports.audit-print-log'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('items.data', 2)
        ->where('items.data.0.id', $newer->id)
        ->where('items.data.0.trial.trial_code', 'TRIAL-AUDIT-1'));
});

test('the per-trial report page shows validation, weighing, and attachment data', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeReportTrial(['trial_code' => 'TRIAL-SHOW-1', 'created_by' => $owner->email, 'progress_status' => 'Draft']);
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
    DB::table('trials_weighing')->insert([
        'trial_id' => $trial->id,
        'section' => 'Packaging',
        'item_no' => 1,
        'weight_value' => '2.15',
        'is_skipped' => 0,
        'created_at' => Carbon::now(),
    ]);

    $response = $this->actingAs($owner)->get(route('trials.report.show', $trial));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('trial.trial_code', 'TRIAL-SHOW-1')
        ->has('results', 1)
        ->where('results.0.decision', 'OK')
        ->has('weighingSections', 2)
        ->where('weighingSections.0.section', 'Packaging')
        ->where('weighingSections.0.stats.count', 1)
        ->where('canEdit', true));
});

test('a user without view access is forbidden from the per-trial report page', function () {
    $outsider = User::factory()->create(['role' => 'Random Role', 'department' => 'Nowhere']);
    $trial = makeReportTrial(['progress_status' => 'In Review']);

    $this->actingAs($outsider)->get(route('trials.report.show', $trial))->assertForbidden();
});

test('a soft-deleted trial 404s on the per-trial report page', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeReportTrial(['created_by' => $owner->email]);
    $trial->deleted_at = Carbon::now();
    $trial->save();

    $this->actingAs($owner)->get(route('trials.report.show', $trial))->assertNotFound();
});

test('printing a report writes both an AuditLog and an ActivityLog row', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeReportTrial(['created_by' => $owner->email]);

    $response = $this->actingAs($owner)->post(route('trials.report.print-log', $trial));

    $response->assertOk();
    $response->assertJson(['ok' => true]);

    $auditLog = AuditLog::where('trial_id', $trial->id)->where('action', 'report_printed')->first();
    expect($auditLog)->not->toBeNull();
    expect($auditLog->new_data)->toBe(['report_type' => 'Report Summary']);

    $activityLog = ActivityLog::where('module', 'REPORT')->where('action', 'PRINT_REPORT')->first();
    expect($activityLog)->not->toBeNull();
    expect($activityLog->record_id)->toBe((string) $trial->id);
});
