<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/trash block in the legacy app's public/index.php: a
 * paginated, filterable list of soft-deleted trials (trials_header.deleted_at
 * IS NOT NULL) with restore. Gated by `manage-settings`
 * (App\Providers\AppServiceProvider), the same is_admin() check legacy
 * performs here.
 *
 * Scope note: legacy's permanent-delete action (`/admin/trash/delete/{id}`)
 * cascades DELETEs across trials_review/trials_weighing/trials_results/
 * trial_attachment_files/trial_edit_permissions/notifications/audit_logs —
 * tables Fase 3 (Inti workflow trial) hasn't ported models for yet. That
 * action is deliberately NOT ported here; this is list + restore only, see
 * ../../../../CLAUDE.md's Trash checklist item for the scope decision.
 */
class TrashController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('manage-settings');

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'deleted_by' => trim((string) $request->query('deleted_by', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        $trials = Trial::query()
            ->whereNotNull('deleted_at')
            ->with(['deletedByUser:id,name,email', 'creator:id,name,email'])
            ->when($filters['q'] !== '', function (Builder $query) use ($filters) {
                $like = '%'.$filters['q'].'%';
                $query->where(function (Builder $q) use ($like) {
                    $q->where('trial_code', 'like', $like)
                        ->orWhere('product_name', 'like', $like)
                        ->orWhere('product_type', 'like', $like);
                });
            })
            ->when($filters['deleted_by'] !== '', fn (Builder $query) => $query->whereHas(
                'deletedByUser',
                fn (Builder $q) => $q->where('name', 'like', '%'.$filters['deleted_by'].'%')
            ))
            ->when(
                $filters['date_from'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_from']) === 1,
                fn (Builder $query) => $query->where('deleted_at', '>=', $filters['date_from'].' 00:00:00')
            )
            ->when(
                $filters['date_to'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_to']) === 1,
                fn (Builder $query) => $query->where('deleted_at', '<=', $filters['date_to'].' 23:59:59')
            )
            ->orderByDesc('deleted_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/trash/index', [
            'trials' => $trials,
            'filters' => $filters,
        ]);
    }

    public function restore(Trial $trial): RedirectResponse
    {
        Gate::authorize('manage-settings');

        if (! $trial->deleted_at) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Trial ini tidak dalam status terhapus.']);

            return to_route('admin.trash.index');
        }

        $trial->deleted_at = null;
        $trial->deleted_by = null;
        $trial->updated_at = Carbon::now();
        $trial->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Trial '.$trial->trial_code.' berhasil direstore.',
        ]);

        return to_route('admin.trash.index');
    }
}
