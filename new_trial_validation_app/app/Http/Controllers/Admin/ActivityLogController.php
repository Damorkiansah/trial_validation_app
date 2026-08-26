<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/activity-logs block in the legacy app's
 * public/index.php: a paginated, filterable, system-wide audit trail
 * (activity_logs — written by logActivity() in legacy's app/bootstrap.php,
 * not yet ported to this app since no trial workflow exists here until
 * Fase 3) with single and bulk permanent delete. Gated by `manage-settings`
 * (App\Providers\AppServiceProvider), the same is_admin() check legacy
 * performs here — no Staff fallback, same as Notifications/Trash.
 */
class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('manage-settings');

        $filters = [
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
            'user' => trim((string) $request->query('user', '')),
            'role' => trim((string) $request->query('role', '')),
            'module' => trim((string) $request->query('module', '')),
            'action' => trim((string) $request->query('action', '')),
            'q' => trim((string) $request->query('q', '')),
        ];

        $logs = ActivityLog::query()
            ->when(
                $filters['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_from']) === 1,
                fn (Builder $query) => $query->where('created_at', '>=', $filters['date_from'].' 00:00:00')
            )
            ->when(
                $filters['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_to']) === 1,
                fn (Builder $query) => $query->where('created_at', '<=', $filters['date_to'].' 23:59:59')
            )
            ->when($filters['user'] !== '', fn (Builder $query) => $query->where('user_name', 'like', '%'.$filters['user'].'%'))
            ->when($filters['role'] !== '', fn (Builder $query) => $query->where('user_role', $filters['role']))
            ->when($filters['module'] !== '', fn (Builder $query) => $query->where('module', $filters['module']))
            ->when($filters['action'] !== '', fn (Builder $query) => $query->where('action', $filters['action']))
            ->when($filters['q'] !== '', function (Builder $query) use ($filters) {
                $like = '%'.$filters['q'].'%';
                $query->where(function (Builder $q) use ($like) {
                    $q->where('record_label', 'like', $like)
                        ->orWhere('old_data', 'like', $like)
                        ->orWhere('new_data', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/activity-logs/index', [
            'logs' => $logs,
            'filters' => $filters,
        ]);
    }

    public function destroy(ActivityLog $activityLog): RedirectResponse
    {
        Gate::authorize('manage-settings');

        $activityLog->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Activity log berhasil dihapus.']);

        return to_route('admin.activity-logs.index');
    }

    public function destroySelected(Request $request): RedirectResponse
    {
        Gate::authorize('manage-settings');

        /** @var array<int, mixed> $rawIds */
        $rawIds = $request->input('log_ids', []);

        $ids = collect($rawIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isNotEmpty()) {
            ActivityLog::query()->whereIn('id', $ids)->delete();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Activity log terpilih berhasil dihapus.']);

        return to_route('admin.activity-logs.index');
    }
}
