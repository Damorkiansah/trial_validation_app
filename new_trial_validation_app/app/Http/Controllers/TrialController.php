<?php

namespace App\Http\Controllers;

use App\Models\MasterOption;
use App\Models\Trial;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the 5 status-grouped trial list pages in the legacy app's
 * public/index.php (lines 221-241): /trials/approved, /trials/in-review,
 * /trials/need-revision, /trials/rejected, /trials/waiting-approval. All
 * share one query shape (Trial::scopeVisibleTo() + Trial::scopeSearch()),
 * just like legacy's trials_list.php view.
 *
 * Read-only by design (Fase 2) — no Action column, see App\Http\Controllers\
 * DashboardController's doc comment for why.
 */
class TrialController extends Controller
{
    /**
     * URL segment => [internal status group, page title, page subtitle].
     * Copied verbatim from the legacy $map array (public/index.php:223-229).
     */
    private const GROUPS = [
        'approved' => ['approved', 'Approved Trials', 'Daftar trial dengan status approved.'],
        'in-review' => ['in-review', 'In Review Trials', 'Trial yang sedang dalam proses review.'],
        'need-revision' => ['need-revision', 'Need Revision Trials', 'Trial yang dikembalikan ke Staff untuk direvisi.'],
        'rejected' => ['rejected', 'Rejected Trials', 'Trial yang ditolak final.'],
        'waiting-approval' => ['waiting', 'Waiting Approval', 'Trial yang menunggu approval Manager QAC.'],
    ];

    public function index(Request $request, string $group): Response
    {
        abort_unless(array_key_exists($group, self::GROUPS), 404);

        [$statusGroup, $title, $subtitle] = self::GROUPS[$group];
        $user = $request->user();

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'product_type' => trim((string) $request->query('product_type', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        $trials = Trial::query()
            ->visibleTo($user, $statusGroup)
            ->search($filters)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $productTypes = MasterOption::query()
            ->where('type', 'product_type')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        return Inertia::render('trials/index', [
            'trials' => $trials,
            'filters' => $filters,
            'productTypes' => $productTypes,
            'pageTitle' => $title,
            'pageSubtitle' => $subtitle,
            'group' => $group,
        ]);
    }
}
