<?php

namespace App\Http\Controllers;

use App\Models\MasterOption;
use App\Models\Trial;
use App\Models\TrialReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /dashboard block in the legacy app's public/index.php
 * (lines 205-219): the current user's scoped trial list (Trial::scopeVisibleTo(),
 * Fase 0) plus summary counts (Trial::summaryCounts()) and the same search
 * filters used by the trials-list pages (Trial::scopeSearch()).
 *
 * Fase 3 adds the "New Trial" button back (legacy's, and per-row Action
 * column now that trials.create/edit exist) — see TrialController.
 */
class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'product_type' => trim((string) $request->query('product_type', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
            'status' => trim((string) $request->query('status', '')),
        ];

        $trials = Trial::query()
            ->visibleTo($user)
            ->search($filters)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $trials->getCollection()->each(function (Trial $trial) use ($user) {
            $trial->setAttribute('can_edit', Gate::forUser($user)->allows('update', $trial));
        });

        $summary = Trial::summaryCounts($user);

        $productTypes = MasterOption::query()
            ->where('type', 'product_type')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        return Inertia::render('dashboard', [
            'trials' => $trials,
            'filters' => $filters,
            'productTypes' => $productTypes,
            'summary' => $summary,
            'canCreateTrial' => Gate::forUser($user)->allows('create', Trial::class),
            'myWork' => $this->myWork($user),
        ]);
    }

    /**
     * "My Work" — a personalized, additive slice of the dashboard: trials
     * this user owns, reviews/approvals specifically waiting on them, and a
     * short recent-activity trail. Deliberately narrower than the shared
     * queue-visibility scopes (Trial::scopeAwaitingApprovalFor(),
     * ReviewController::index()'s department scope) used elsewhere — this
     * only surfaces what's really this user's to act on, not everything
     * they're merely allowed to see.
     *
     * @return array<string, mixed>
     */
    private function myWork(User $user): array
    {
        $myTrialsQuery = Trial::query()
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(TRIM(created_by)) = ?', [strtolower(trim($user->email))])
            ->whereNotIn('progress_status', ['Approved', 'Rejected']);

        $myTrials = (clone $myTrialsQuery)->orderByDesc('updated_at')->limit(5)->get();
        $myTrialsTotal = (clone $myTrialsQuery)->count();

        $pendingReviews = collect();
        $pendingReviewsTotal = 0;
        $recentlyReviewed = collect();
        if ($user->isReviewer()) {
            $departments = $user->reviewDepartmentsForUser();

            $reviewQuery = TrialReview::query()
                ->join('trials_header as h', 'h.id', '=', 'trials_review.trial_id')
                ->where('h.progress_status', 'In Review')
                ->whereRaw('trials_review.review_round = h.revision_no + 1')
                ->where('trials_review.status', 'Pending')
                ->when(
                    $departments,
                    fn (Builder $q) => $q->whereIn(DB::raw('UPPER(TRIM(trials_review.department))'), $departments),
                    fn (Builder $q) => $q->whereRaw('1 = 0'),
                );

            $pendingReviewsTotal = (clone $reviewQuery)->count();
            $pendingReviews = (clone $reviewQuery)
                ->orderByDesc('trials_review.id')
                ->limit(5)
                ->get(['trials_review.id', 'trials_review.trial_id', 'trials_review.department', 'h.trial_code', 'h.product_name']);

            $recentlyReviewed = TrialReview::query()
                ->where('reviewer_email', $user->email)
                ->where('status', 'Reviewed')
                ->whereNotNull('reviewed_at')
                ->with('trial:id,trial_code,product_name')
                ->orderByDesc('reviewed_at')
                ->limit(3)
                ->get();
        }

        $pendingApprovals = collect();
        $pendingApprovalsTotal = 0;
        $recentlyDecided = collect();
        if ($user->canApproveTrials()) {
            $approvalQuery = Trial::query()
                ->where('progress_status', 'Ready for Approval')
                ->whereNull('deleted_at')
                ->where('approver_user_id', $user->id);

            $pendingApprovalsTotal = (clone $approvalQuery)->count();
            $pendingApprovals = (clone $approvalQuery)
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get(['id', 'trial_code', 'product_name', 'updated_at']);

            $recentlyDecided = Trial::query()
                ->where('approver_user_id', $user->id)
                ->whereNotNull('final_decision')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get(['id', 'trial_code', 'product_name', 'final_decision', 'updated_at']);
        }

        return [
            'myTrials' => $myTrials->map(fn (Trial $t) => [
                'id' => $t->id,
                'trial_code' => $t->trial_code,
                'product_name' => $t->product_name,
                'progress_status' => $t->progress_status,
                'current_step' => $t->current_step,
                'pending_with' => $t->pending_with,
            ])->values(),
            'myTrialsTotal' => $myTrialsTotal,
            'pendingReviews' => $pendingReviews->map(fn (TrialReview $r) => [
                'id' => $r->id,
                'trial_id' => $r->trial_id,
                'trial_code' => $r->getAttribute('trial_code'),
                'product_name' => $r->getAttribute('product_name'),
                'department' => $r->department,
            ])->values(),
            'pendingReviewsTotal' => $pendingReviewsTotal,
            'recentlyReviewed' => $recentlyReviewed->map(fn (TrialReview $r) => [
                'id' => $r->id,
                'trial_id' => $r->trial_id,
                'trial_code' => $r->trial?->trial_code,
                'product_name' => $r->trial?->product_name,
                'reviewed_at' => $r->reviewed_at?->toDateTimeString(),
            ])->values(),
            'pendingApprovals' => $pendingApprovals->map(fn (Trial $t) => [
                'id' => $t->id,
                'trial_code' => $t->trial_code,
                'product_name' => $t->product_name,
            ])->values(),
            'pendingApprovalsTotal' => $pendingApprovalsTotal,
            'recentlyDecided' => $recentlyDecided->map(fn (Trial $t) => [
                'id' => $t->id,
                'trial_code' => $t->trial_code,
                'product_name' => $t->product_name,
                'final_decision' => $t->final_decision,
            ])->values(),
        ];
    }
}
