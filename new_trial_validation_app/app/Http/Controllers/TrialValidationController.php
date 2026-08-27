<?php

namespace App\Http\Controllers;

use App\Actions\Trials\SaveTrialValidation;
use App\Http\Requests\Trials\SaveTrialValidationRequest;
use App\Models\Trial;
use App\Models\ValidationParameter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /trials/{id}/validation (GET) and /trials/{id}/validation/save
 * (POST) handlers in the legacy app's public/index.php:493-559 — wizard
 * Step 2. A separate controller from TrialController, matching this app's
 * one-controller-per-feature-slice convention (Weighing/Attachment/Review
 * will each need their own edit/update pair later).
 */
class TrialValidationController extends Controller
{
    public function edit(int $trial): Response
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        Gate::authorize('view', $trial);

        $parameters = ValidationParameter::query()
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->where('product_type', $trial->product_type)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'parameter_name', 'specification']);

        $results = $trial->results()->get()->keyBy('parameter_id');

        return Inertia::render('trials/validation', [
            'trial' => $trial,
            'parameters' => $parameters,
            'results' => $results,
            'canEdit' => Gate::allows('update', $trial),
        ]);
    }

    public function update(SaveTrialValidationRequest $request, int $trial, SaveTrialValidation $action): RedirectResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        $action($trial, $request->validated('results'), $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Validation berhasil disimpan.']);

        return to_route('trials.validation.edit', $trial);
    }
}
