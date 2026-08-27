<?php

use App\Models\ActivityLog;
use App\Models\Trial;
use App\Models\User;
use App\Models\ValidationParameter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

function makeValidationTrial(array $attributes = []): Trial
{
    return Trial::create([
        'trial_code' => $attributes['trial_code'] ?? 'TRIAL-VAL-1',
        'product_name' => 'Sample Product',
        'product_type' => $attributes['product_type'] ?? 'Tube',
        'progress_status' => $attributes['progress_status'] ?? 'Draft',
        'current_step' => $attributes['current_step'] ?? 'Validation',
        'created_by' => $attributes['created_by'] ?? 'owner@local.test',
    ]);
}

function makeValidationParameter(string $productType, string $name, int $sortOrder = 0): ValidationParameter
{
    return ValidationParameter::create([
        'product_type' => $productType,
        'parameter_name' => $name,
        'specification' => 'Spec for '.$name,
        'sort_order' => $sortOrder,
    ]);
}

test('a non-draft trial is viewable by a viewer with canEdit false', function () {
    $viewer = User::factory()->role('Viewer')->create();
    $trial = makeValidationTrial(['progress_status' => 'In Review']);

    $response = $this->actingAs($viewer)->get(route('trials.validation.edit', $trial));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('canEdit', false));
});

test('a staff member without ownership cannot view a draft trial validation page', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $otherStaff = User::factory()->create(['email' => 'other@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);

    $this->actingAs($otherStaff)->get(route('trials.validation.edit', $trial))->assertForbidden();
});

test('a soft-deleted trial 404s on the validation page', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);
    $trial->deleted_at = Carbon::now();
    $trial->save();

    $this->actingAs($owner)->get(route('trials.validation.edit', $trial))->assertNotFound();
});

test('saving validation results persists rows and advances current_step', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);
    $ok = makeValidationParameter('Tube', 'Weight', 1);
    $notOk = makeValidationParameter('Tube', 'Leak Test', 2);
    $na = makeValidationParameter('Tube', 'Color', 3);

    $response = $this->actingAs($owner)->put(route('trials.validation.update', $trial), [
        'results' => [
            ['parameter_id' => $ok->id, 'decision' => 'OK', 'result' => '', 'remark' => ''],
            ['parameter_id' => $notOk->id, 'decision' => 'NOT OK', 'result' => 'Cracked', 'remark' => 'Found a crack'],
            ['parameter_id' => $na->id, 'decision' => 'N/A', 'result' => '', 'remark' => ''],
        ],
    ]);

    $response->assertRedirect(route('trials.weighing.edit', ['trial' => $trial, 'section' => 'Packaging']));

    $rows = DB::table('trials_results')->where('trial_id', $trial->id)->get()->keyBy('parameter_id');
    expect($rows)->toHaveCount(3);
    expect($rows[$ok->id]->result_value)->toBe('Conform');
    expect($rows[$notOk->id]->result_value)->toBe('Cracked');
    expect($rows[$notOk->id]->remark)->toBe('Found a crack');
    expect($rows[$na->id]->result_value)->toBe('N/A');

    expect($trial->fresh()->current_step)->toBe('WeighingPackaging');
    expect($trial->fresh()->progress_status)->toBe('Draft');

    $log = ActivityLog::where('module', 'VALIDATION')->where('action', 'UPDATE')->first();
    expect($log)->not->toBeNull();
    expect($log->record_id)->toBe((string) $trial->id);
});

test('a missing parameter row is rejected and nothing is persisted', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);
    $first = makeValidationParameter('Tube', 'Weight', 1);
    makeValidationParameter('Tube', 'Leak Test', 2);

    $response = $this->actingAs($owner)->put(route('trials.validation.update', $trial), [
        'results' => [
            ['parameter_id' => $first->id, 'decision' => 'OK', 'result' => '', 'remark' => ''],
        ],
    ]);

    $response->assertSessionHasErrors('results');
    expect(DB::table('trials_results')->where('trial_id', $trial->id)->count())->toBe(0);
    expect($trial->fresh()->current_step)->toBe('Validation');
});

test('an invalid decision value is rejected', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);
    $param = makeValidationParameter('Tube', 'Weight', 1);

    $response = $this->actingAs($owner)->put(route('trials.validation.update', $trial), [
        'results' => [
            ['parameter_id' => $param->id, 'decision' => 'MAYBE', 'result' => '', 'remark' => ''],
        ],
    ]);

    $response->assertSessionHasErrors('results.0.decision');
    expect(DB::table('trials_results')->where('trial_id', $trial->id)->count())->toBe(0);
});

test('a NOT OK decision missing result or remark is rejected', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);
    $param = makeValidationParameter('Tube', 'Weight', 1);

    $response = $this->actingAs($owner)->put(route('trials.validation.update', $trial), [
        'results' => [
            ['parameter_id' => $param->id, 'decision' => 'NOT OK', 'result' => '', 'remark' => ''],
        ],
    ]);

    $response->assertSessionHasErrors('results');
    expect(DB::table('trials_results')->where('trial_id', $trial->id)->count())->toBe(0);
});

test('re-saving validation results updates existing rows in place', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);
    $param = makeValidationParameter('Tube', 'Weight', 1);

    $this->actingAs($owner)->put(route('trials.validation.update', $trial), [
        'results' => [
            ['parameter_id' => $param->id, 'decision' => 'OK', 'result' => '', 'remark' => ''],
        ],
    ]);

    $response = $this->actingAs($owner)->put(route('trials.validation.update', $trial), [
        'results' => [
            ['parameter_id' => $param->id, 'decision' => 'NOT OK', 'result' => 'Too heavy', 'remark' => 'Exceeds tolerance'],
        ],
    ]);

    $response->assertRedirect(route('trials.weighing.edit', ['trial' => $trial, 'section' => 'Packaging']));
    $rows = DB::table('trials_results')->where('trial_id', $trial->id)->get();
    expect($rows)->toHaveCount(1);
    expect($rows->first()->decision)->toBe('NOT OK');
    expect($rows->first()->result_value)->toBe('Too heavy');
});

test('a staff member without edit rights is forbidden from saving validation results', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $otherStaff = User::factory()->create(['email' => 'other@local.test']);
    $trial = makeValidationTrial(['created_by' => $owner->email]);
    $param = makeValidationParameter('Tube', 'Weight', 1);

    $this->actingAs($otherStaff)->put(route('trials.validation.update', $trial), [
        'results' => [
            ['parameter_id' => $param->id, 'decision' => 'OK', 'result' => '', 'remark' => ''],
        ],
    ])->assertForbidden();
});
