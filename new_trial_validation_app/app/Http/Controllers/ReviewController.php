<?php

namespace App\Http\Controllers;

use App\Actions\Trials\SaveDepartmentReview;
use App\Http\Requests\Reviews\SaveDepartmentReviewRequest;
use App\Models\TrialReview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /reviews (GET) and /review/{id}/save (POST) handlers in the
 * legacy app's public/index.php:796-857 — the per-department reviewer's
 * inbox, entirely separate from the trial-owner-facing wizard (see
 * App\Http\Controllers\TrialReviewController for that side). Only trials
 * currently In Review, in their current review round, show up here —
 * matches legacy's `review_round=h.revision_no+1` join condition.
 */
class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', TrialReview::class);

        $departments = $request->user()->reviewDepartmentsForUser();

        $reviews = TrialReview::query()
            ->join('trials_header as h', 'h.id', '=', 'trials_review.trial_id')
            ->where('h.progress_status', 'In Review')
            ->whereRaw('trials_review.review_round = h.revision_no + 1')
            ->when($departments, fn (Builder $q) => $q->whereIn(DB::raw('UPPER(TRIM(trials_review.department))'), $departments), fn (Builder $q) => $q->whereRaw('1 = 0'))
            ->orderByRaw("trials_review.status = 'Pending' desc")
            ->orderByDesc('trials_review.id')
            ->select('trials_review.*', 'h.trial_code', 'h.product_name', 'h.revision_no', 'h.progress_status')
            ->paginate(20)
            ->withQueryString();

        $reviews->getCollection()->each(function (TrialReview $review) {
            $review->setAttribute(
                'active',
                $review->getAttribute('progress_status') === 'In Review'
                    && (int) $review->review_round === (int) $review->getAttribute('revision_no') + 1
                    && $review->status === 'Pending',
            );
        });

        return Inertia::render('reviews/index', [
            'items' => $reviews,
        ]);
    }

    public function update(SaveDepartmentReviewRequest $request, int $review, SaveDepartmentReview $action): RedirectResponse
    {
        $review = TrialReview::findOrFail($review);

        $action($review, trim((string) $request->string('comment')), $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Review berhasil disimpan.']);

        return to_route('reviews.index');
    }
}
