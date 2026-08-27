<?php

namespace App\Http\Controllers;

use App\Actions\Trials\SaveTrialWeighing;
use App\Http\Requests\Trials\SaveTrialWeighingRequest;
use App\Models\Trial;
use App\Models\TrialWeighing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /trials/{id}/weighing/{section} (GET) and
 * /trials/{id}/weighing/{section}/save (POST) handlers in the legacy app's
 * public/index.php:561-617 — wizard Steps 3 (Packaging) and 4 (Filling).
 * Separate controller from TrialController/TrialValidationController,
 * matching this app's one-controller-per-feature-slice convention.
 */
class TrialWeighingController extends Controller
{
    public function edit(int $trial, string $section): Response
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        Gate::authorize('view', $trial);

        $rows = TrialWeighing::query()
            ->where('trial_id', $trial->id)
            ->where('section', $section)
            ->orderBy('item_no')
            ->get();

        return Inertia::render('trials/weighing', [
            'trial' => $trial,
            'section' => $section,
            'values' => $rows->pluck('weight_value', 'item_no'),
            'skip' => $rows->contains(fn (TrialWeighing $row) => $row->is_skipped),
            'canEdit' => Gate::allows('update', $trial),
        ]);
    }

    public function update(SaveTrialWeighingRequest $request, int $trial, string $section, SaveTrialWeighing $action): RedirectResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        $action(
            $trial,
            $section,
            $request->boolean('skip'),
            (array) $request->input('w', []),
            $request->user(),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Weighing berhasil disimpan.']);

        if ($section === 'Packaging') {
            return to_route('trials.weighing.edit', ['trial' => $trial, 'section' => 'Filling']);
        }

        return to_route('trials.attachments.edit', $trial);
    }
}
