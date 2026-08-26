<?php

use App\Models\ActivityLog;
use App\Models\MasterOption;
use App\Models\Product;
use App\Models\Trial;
use App\Models\TrialEditPermission;
use App\Models\User;
use Illuminate\Support\Carbon;

function makeTrialProduct(array $attributes = []): Product
{
    // Product's #[Fillable] only covers product_name/finish_good_code — is_active
    // and deleted_at are set via direct property assignment, matching the
    // pattern ProductController::destroy() already uses (see
    // laravel_mutation_gotchas memory: Fillable exclusions silently no-op on
    // mass-assignment instead of erroring).
    $product = Product::create([
        'product_name' => $attributes['product_name'] ?? 'Sample Product',
        'finish_good_code' => $attributes['finish_good_code'] ?? 'FG-001',
    ]);
    $product->is_active = $attributes['is_active'] ?? true;
    $product->deleted_at = $attributes['deleted_at'] ?? null;
    $product->save();

    return $product;
}

function seedTrialMasterOptions(): void
{
    $seed = [
        'product_type' => ['Tube'],
        'validation_category' => ['New Product'],
        'validation_scope' => ['Retail', 'Bulk'],
        'machine_used' => ['Line A', 'Line B'],
    ];

    foreach ($seed as $type => $names) {
        foreach ($names as $i => $name) {
            MasterOption::create(['type' => $type, 'name' => $name, 'sort_order' => $i, 'is_active' => true]);
        }
    }
}

function validTrialPayload(Product $product): array
{
    return [
        'product_id' => $product->id,
        'product_type' => 'Tube',
        'validation_date' => '2026-08-20',
        'validation_category' => 'New Product',
        'risk_level' => 'Low',
        'validation_scope' => ['Retail'],
        'machine_used' => ['Line A'],
        'estimate_qty' => 100,
        'batch_number' => 'B-001',
        'bulk_code' => 'BULK-001',
        'support_team' => 'QA Team',
        'initiated_person_team' => 'John Doe',
        'reason' => 'New product validation',
        'bom' => 'BOM details',
    ];
}

beforeEach(function () {
    seedTrialMasterOptions();
});

test('staff can create a trial', function () {
    $staff = User::factory()->create(['email' => 'owner@local.test']);
    $product = makeTrialProduct();

    $response = $this->actingAs($staff)->post(route('trials.store'), validTrialPayload($product));

    $trial = Trial::first();
    $response->assertRedirect(route('trials.edit', $trial));

    expect($trial->progress_status)->toBe('Draft');
    expect($trial->current_step)->toBe('Validation');
    expect($trial->product_name)->toBe($product->product_name);
    expect($trial->finish_good_code)->toBe($product->finish_good_code);
    expect($trial->created_by)->toBe('owner@local.test');
    expect($trial->trial_code)->toStartWith('TRIAL-');
    expect($trial->validation_scope)->toBe(['Retail']);

    $log = ActivityLog::where('module', 'TRIAL')->where('action', 'CREATE')->first();
    expect($log)->not->toBeNull();
    expect($log->record_label)->toBe($trial->trial_code);
});

test('non-staff cannot create a trial', function () {
    $viewer = User::factory()->role('Viewer')->create();
    $product = makeTrialProduct();

    $this->actingAs($viewer)->get(route('trials.create'))->assertForbidden();
    $this->actingAs($viewer)->post(route('trials.store'), validTrialPayload($product))->assertForbidden();
});

test('creating a trial requires the core fields', function () {
    $staff = User::factory()->create();
    $product = makeTrialProduct();

    $response = $this->actingAs($staff)->post(route('trials.store'), collect(validTrialPayload($product))->except('batch_number')->all());

    $response->assertSessionHasErrors('batch_number');
});

test('validation_scope must have at least one value', function () {
    $staff = User::factory()->create();
    $product = makeTrialProduct();

    $response = $this->actingAs($staff)->post(route('trials.store'), [
        ...validTrialPayload($product),
        'validation_scope' => [],
    ]);

    $response->assertSessionHasErrors('validation_scope');
});

test('an unknown master-option value is rejected', function () {
    $staff = User::factory()->create();
    $product = makeTrialProduct();

    $response = $this->actingAs($staff)->post(route('trials.store'), [
        ...validTrialPayload($product),
        'product_type' => 'Not A Real Type',
    ]);

    $response->assertSessionHasErrors('product_type');
});

test('an invalid risk level is rejected', function () {
    $staff = User::factory()->create();
    $product = makeTrialProduct();

    $response = $this->actingAs($staff)->post(route('trials.store'), [
        ...validTrialPayload($product),
        'risk_level' => 'Extreme',
    ]);

    $response->assertSessionHasErrors('risk_level');
});

test('an inactive product cannot be selected', function () {
    $staff = User::factory()->create();
    $product = makeTrialProduct(['is_active' => false]);

    $response = $this->actingAs($staff)->post(route('trials.store'), validTrialPayload($product));

    $response->assertSessionHasErrors('product_id');
});

test('a soft-deleted product cannot be selected', function () {
    $staff = User::factory()->create();
    $product = makeTrialProduct(['deleted_at' => Carbon::now()]);

    $response = $this->actingAs($staff)->post(route('trials.store'), validTrialPayload($product));

    $response->assertSessionHasErrors('product_id');
});

test('a draft trial is editable by its owner', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $product = makeTrialProduct();
    $trial = Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-1', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Draft', 'created_by' => 'owner@local.test']);

    $this->actingAs($owner)->get(route('trials.edit', $trial))->assertOk();

    $response = $this->actingAs($owner)->put(route('trials.update', $trial), [
        ...validTrialPayload($product),
        'batch_number' => 'B-UPDATED',
    ]);

    $response->assertRedirect(route('trials.edit', $trial));
    expect($trial->fresh()->batch_number)->toBe('B-UPDATED');
    expect($trial->fresh()->progress_status)->toBe('Draft');
    expect($trial->fresh()->current_step)->toBe('Validation');
    expect($trial->fresh()->trial_code)->toBe('TRIAL-1');
    expect($trial->fresh()->revision_no)->toBe(0);

    $log = ActivityLog::where('module', 'TRIAL')->where('action', 'UPDATE')->first();
    expect($log)->not->toBeNull();
});

test('a draft trial is not editable by an unrelated staff member', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $otherStaff = User::factory()->create(['email' => 'other@local.test']);
    $product = makeTrialProduct();
    $trial = Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-1', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Draft', 'created_by' => $owner->email]);

    $this->actingAs($otherStaff)->get(route('trials.edit', $trial))->assertForbidden();
    $this->actingAs($otherStaff)->put(route('trials.update', $trial), validTrialPayload($product))->assertForbidden();
});

test('a super admin without ownership or a grant cannot edit a draft trial', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $product = makeTrialProduct();
    $trial = Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-1', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Draft', 'created_by' => $owner->email]);

    $this->actingAs($superAdmin)->get(route('trials.edit', $trial))->assertForbidden();
});

test('a staff member with an active edit-permission grant can edit a draft trial', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $grantee = User::factory()->create(['email' => 'grantee@local.test']);
    $product = makeTrialProduct();
    $trial = Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-1', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Draft', 'created_by' => $owner->email]);

    TrialEditPermission::create([
        'trial_id' => $trial->id,
        'user_id' => $grantee->id,
        'can_edit' => true,
        'granted_at' => Carbon::now(),
    ]);

    $this->actingAs($grantee)->get(route('trials.edit', $trial))->assertOk();
});

test('a need-revision trial is editable by any staff member', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $otherStaff = User::factory()->create(['email' => 'other@local.test']);
    $product = makeTrialProduct();
    $trial = Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-1', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Need Revision', 'created_by' => $owner->email]);

    $this->actingAs($otherStaff)->get(route('trials.edit', $trial))->assertOk();
});

test('an approved trial cannot be edited', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $product = makeTrialProduct();
    $trial = Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-1', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Approved', 'created_by' => $owner->email]);

    $this->actingAs($owner)->get(route('trials.edit', $trial))->assertForbidden();
});

test('a soft-deleted trial 404s on edit', function () {
    $owner = User::factory()->create(['email' => 'owner@local.test']);
    $product = makeTrialProduct();
    $trial = Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-1', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Draft', 'created_by' => $owner->email]);
    // deleted_at isn't in Trial's #[Fillable] list — set it directly.
    $trial->deleted_at = Carbon::now();
    $trial->save();

    $this->actingAs($owner)->get(route('trials.edit', $trial))->assertNotFound();
});

test('the draft group appears on the trials list route', function () {
    $superAdmin = User::factory()->create(['role' => 'Super Admin']);
    $product = makeTrialProduct();
    Trial::create([...validTrialPayload($product), 'trial_code' => 'TRIAL-DRAFT', 'product_name' => $product->product_name, 'finish_good_code' => $product->finish_good_code, 'progress_status' => 'Draft', 'created_by' => $superAdmin->email]);

    $response = $this->actingAs($superAdmin)->get(route('trials.index', 'draft'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('trials.data', fn ($data) => collect($data)->pluck('trial_code')->contains('TRIAL-DRAFT')));
});
