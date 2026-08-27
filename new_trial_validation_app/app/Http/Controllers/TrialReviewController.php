<?php

namespace App\Http\Controllers;

use App\Actions\Trials\CheckTrialCompleteness;
use App\Actions\Trials\SubmitTrialForReview;
use App\Http\Requests\Trials\SubmitTrialForReviewRequest;
use App\Models\Trial;
use App\Models\TrialReview;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Wizard Step 6 (Review & Submit) — the trial owner's side of legacy's
 * Department Review workflow. Legacy has no dedicated page for this: the
 * "Submit for Review" button + department/approver picker live inside the
 * Report page (app/views/report.php, public/index.php:740-793), which
 * itself is a separate, still-deferred Fase 3 sub-item (Reports). This
 * controller ports just the submit-for-review action and a completeness/
 * status view for it, leaving the full print-style report for later.
 *
 * The reviewer's own side (the /reviews department inbox and /review/{id}/save
 * action) is a separate, non-wizard feature — see App\Http\Controllers\ReviewController.
 */
class TrialReviewController extends Controller
{
    public function edit(int $trial): Response
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        Gate::authorize('view', $trial);

        $reviews = $trial->reviews()->orderBy('review_round')->orderBy('department')->get();

        $approvers = User::query()
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return Inertia::render('trials/review', [
            'trial' => $trial,
            'reviewerDepartments' => User::reviewerDepartmentCodes(),
            'reviews' => $reviews->map(fn (TrialReview $r) => [
                'department' => $r->department,
                'review_round' => $r->review_round,
                'status' => $r->status,
                'reviewer_name' => $r->reviewer_name,
                'reviewed_at' => $r->reviewed_at?->toDateTimeString(),
                'comment' => $r->comment,
            ]),
            'approvers' => $approvers->map(fn (User $u) => [
                'id' => $u->id,
                'label' => trim((string) ($u->name ?: $u->email)).' - '.$u->role,
            ]),
            'selectedApproverId' => $trial->approver_user_id,
            'completeness' => (new CheckTrialCompleteness)($trial),
            'canEdit' => Gate::allows('update', $trial),
        ]);
    }

    public function store(SubmitTrialForReviewRequest $request, int $trial, SubmitTrialForReview $action): RedirectResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);
        $approver = User::where('is_active', 1)->whereNull('deleted_at')->findOrFail($request->integer('approver_user_id'));

        $action($trial, $request->departments(), $approver, $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Trial berhasil dikirim untuk review.']);

        return to_route('trials.review.edit', $trial);
    }
}
