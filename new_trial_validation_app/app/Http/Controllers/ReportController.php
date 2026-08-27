<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MasterOption;
use App\Models\Trial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of legacy's /report hub + 5 report list pages
 * (public/index.php:243-329, app/views/report_*.php). No extra Gate beyond
 * the standard auth+verified middleware — legacy's /report/* routes have no
 * role check either, every method here just applies Trial::visibleTo() for
 * the same row-level scoping every other list page already uses.
 */
class ReportController extends Controller
{
    /**
     * ->through() on a LengthAwarePaginator<int, Trial> confuses PHPStan's
     * generic resolution (an "unresolvable type" error with no useful
     * suggestion) — building the paginated array by hand instead sidesteps
     * that entirely.
     *
     * @template TItem
     *
     * @param  LengthAwarePaginator<int, Trial>  $paginator
     * @param  callable(Trial): TItem  $map
     * @return array{data: array<int, TItem>, current_page: int, last_page: int, per_page: int, total: int}
     */
    private function paginatedArray(LengthAwarePaginator $paginator, callable $map): array
    {
        return [
            'data' => $paginator->getCollection()->map($map)->values()->all(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    public function index(): Response
    {
        return Inertia::render('reports/index');
    }

    public function approved(Request $request): Response
    {
        $items = Trial::query()
            ->visibleTo($request->user(), 'approved')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('reports/approved', [
            'items' => $this->paginatedArray($items, fn (Trial $trial) => [
                'id' => $trial->id,
                'trial_code' => $trial->trial_code,
                'product_name' => $trial->product_name,
                'finish_good_code' => $trial->finish_good_code,
                'product_type' => $trial->product_type,
                'approved_at' => $trial->approved_at?->toDateTimeString(),
                'approved_by' => $trial->approved_by ? User::displayName($trial->approved_by) : null,
            ]),
        ]);
    }

    public function rejected(Request $request): Response
    {
        $items = Trial::query()
            ->visibleTo($request->user(), 'rejected')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('reports/rejected', [
            'items' => $this->paginatedArray($items, fn (Trial $trial) => [
                'id' => $trial->id,
                'trial_code' => $trial->trial_code,
                'product_name' => $trial->product_name,
                'finish_good_code' => $trial->finish_good_code,
                'product_type' => $trial->product_type,
                'rejected_at' => $trial->rejected_at?->toDateTimeString(),
                'rejected_by' => $trial->rejected_by ? User::displayName($trial->rejected_by) : null,
                'approval_comment' => $trial->approval_comment,
            ]),
        ]);
    }

    public function trialSummary(Request $request): Response
    {
        $filters = [
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
            'status' => trim((string) $request->query('status', '')),
            'product_type' => trim((string) $request->query('product_type', '')),
            'validation_scope' => trim((string) $request->query('validation_scope', '')),
            'machine_used' => trim((string) $request->query('machine_used', '')),
            'product_name' => trim((string) $request->query('product_name', '')),
        ];

        $items = Trial::query()
            ->visibleTo($request->user())
            ->trialSummaryFilters($filters)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $option = fn (string $type) => MasterOption::query()
            ->where('type', $type)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        return Inertia::render('reports/trial-summary', [
            'items' => $items,
            'filters' => $filters,
            'productTypes' => $option('product_type'),
            'validationScopes' => $option('validation_scope'),
            'machines' => $option('machine_used'),
        ]);
    }

    public function departmentReview(Request $request): Response
    {
        $items = Trial::query()
            ->visibleTo($request->user())
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('reports/department-review', [
            'items' => $this->paginatedArray($items, function (Trial $trial) {
                $statuses = array_map(fn (array $entry) => $entry['status'], $trial->reviewStatusByDepartment());
                $required = array_filter($statuses, fn (string $status) => $status !== 'N/A');

                $reviewStatus = 'N/A';
                if ($required) {
                    $reviewStatus = in_array('Pending', $required, true) ? 'Pending' : 'Reviewed';
                }

                return [
                    'id' => $trial->id,
                    'trial_code' => $trial->trial_code,
                    'product_name' => $trial->product_name,
                    'pending_with' => $trial->pending_with,
                    'departments' => $statuses,
                    'review_status' => $reviewStatus,
                ];
            }),
            'reviewerDepartments' => User::reviewerDepartmentCodes(),
        ]);
    }

    public function auditPrintLog(): Response
    {
        $items = AuditLog::query()
            ->where('action', 'report_printed')
            ->with('trial:id,trial_code')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('reports/audit-print-log', [
            'items' => $items,
        ]);
    }
}
