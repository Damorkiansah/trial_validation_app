<?php

namespace App\Http\Controllers;

use App\Actions\Trials\CheckTrialCompleteness;
use App\Actions\Trials\RecordReportPrint;
use App\Models\Trial;
use App\Models\TrialAttachmentFile;
use App\Models\TrialResult;
use App\Models\TrialReview;
use App\Models\TrialWeighing;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of legacy's per-trial Report Summary page (app/views/report.php,
 * public/index.php:740-793) and its print-log endpoint
 * (public/index.php:330-337) — the rich read/print view only. The
 * Submit-for-Review form that also lives on legacy's report.php was already
 * carved out into its own wizard Step 6 (see App\Http\Controllers\TrialReviewController),
 * so this controller doesn't duplicate it — show() only surfaces a
 * completeness note plus a link to that page when applicable.
 *
 * Also doubles as the single "detail" page an approver/reviewer lands on from
 * the Approval Queue / Review Queue (see ApprovalController::index() /
 * ReviewController::index()) — canApprove/pendingReviews below let this same
 * page carry the actual decision/review action inline, so acting on a trial
 * never requires bouncing back out to the list.
 */
class TrialReportController extends Controller
{
    public function show(Request $request, int $trial): Response
    {
        $trial = Trial::whereNull('deleted_at')->with(['product', 'approver'])->findOrFail($trial);

        Gate::authorize('view', $trial);

        $user = $request->user();

        $canApprove = $trial->progress_status === 'Ready for Approval' && Gate::allows('approve', $trial);

        $pendingReviews = collect();
        if ($trial->progress_status === 'In Review' && $user->isReviewer()) {
            $departments = $user->reviewDepartmentsForUser();

            $pendingReviews = TrialReview::query()
                ->where('trial_id', $trial->id)
                ->where('review_round', $trial->currentReviewRound())
                ->where('status', 'Pending')
                ->when(
                    $departments,
                    fn ($q) => $q->whereIn(DB::raw('UPPER(TRIM(department))'), $departments),
                    fn ($q) => $q->whereRaw('1 = 0'),
                )
                ->get(['id', 'department']);
        }

        $results = TrialResult::query()
            ->where('trial_id', $trial->id)
            ->with('parameter')
            ->get()
            ->sortBy(fn (TrialResult $r) => sprintf('%010d-%010d', $r->parameter->sort_order, $r->parameter_id))
            ->values();

        $weighings = TrialWeighing::query()
            ->where('trial_id', $trial->id)
            ->orderBy('item_no')
            ->get()
            ->groupBy('section');

        $weighingSections = collect(['Packaging', 'Filling'])->map(function (string $section) use ($weighings) {
            $items = $weighings->get($section, collect());

            return [
                'section' => $section,
                'stats' => TrialWeighing::statsForSection($items),
            ];
        });

        $attachments = TrialAttachmentFile::query()
            ->where('trial_id', $trial->id)
            ->whereNull('deleted_at')
            ->orderBy('category')
            ->orderBy('id')
            ->get()
            ->groupBy('category')
            ->map(fn ($files) => $files->map(fn (TrialAttachmentFile $file) => [
                'id' => $file->id,
                'file_name' => $file->file_name,
                'url' => route('trials.attachments.show', [$trial->id, $file->id]),
            ])->values());

        $reviewByDept = $trial->reviewStatusByDepartment();

        $approvalBlockedNote = null;
        if ($trial->progress_status === 'Ready for Approval' && ! $canApprove && $user->canApproveTrials()) {
            $approvalBlockedNote = 'Menunggu approval oleh '.($trial->pending_with ?: 'approver lain').', bukan giliran Anda.';
        }

        $reviewCompletedNote = null;
        if ($trial->progress_status === 'In Review' && $user->isReviewer() && $pendingReviews->isEmpty()) {
            $myDepartments = $user->reviewDepartmentsForUser();
            foreach ($reviewByDept as $dept => $entry) {
                if ($entry['status'] === 'Reviewed' && in_array(User::normalizeDepartment($dept), $myDepartments, true)) {
                    $reviewCompletedNote = 'Anda sudah menyelesaikan review department Anda untuk trial ini.';
                    break;
                }
            }
        }

        return Inertia::render('trials/report', [
            'trial' => $trial,
            'results' => $results->map(fn (TrialResult $r) => [
                'parameter_name' => $r->parameter->parameter_name,
                'specification' => $r->parameter->specification,
                'decision' => $r->decision,
                'result_value' => $r->result_value,
                'remark' => $r->remark,
            ])->values(),
            'weighingSections' => $weighingSections,
            'attachments' => $attachments,
            'reviews' => collect($reviewByDept)->map(fn (array $entry, string $dept) => [
                'department' => $dept,
                'review_round' => $trial->currentReviewRound(),
                'status' => $entry['status'],
                'reviewer_name' => $entry['review']?->reviewer_name ? User::displayName($entry['review']->reviewer_name) : null,
                'reviewed_at' => $entry['review']?->reviewed_at?->toDateTimeString(),
                'comment' => $entry['review']?->comment,
            ])->values(),
            'approvedByName' => $trial->approved_by ? User::displayName($trial->approved_by) : null,
            'rejectedByName' => $trial->rejected_by ? User::displayName($trial->rejected_by) : null,
            'completeness' => (new CheckTrialCompleteness)($trial),
            'canEdit' => Gate::allows('update', $trial),
            'canApprove' => $canApprove,
            'pendingReviews' => $pendingReviews->map(fn (TrialReview $r) => [
                'id' => $r->id,
                'department' => $r->department,
            ])->values(),
            'approvalBlockedNote' => $approvalBlockedNote,
            'reviewCompletedNote' => $reviewCompletedNote,
        ]);
    }

    public function logPrint(Request $request, int $trial, RecordReportPrint $action): JsonResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        Gate::authorize('view', $trial);

        $action($trial, $request->user());

        return response()->json(['ok' => true]);
    }
}
