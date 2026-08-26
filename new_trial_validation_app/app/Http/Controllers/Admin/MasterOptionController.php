<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMasterOptionRequest;
use App\Models\MasterOption;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/masters (/templates/master) block in the legacy app's
 * public/index.php: paginated list with edit-in-place, create-or-update-by-
 * id-or-(type,name) upsert, soft delete. Gated by the existing `manage-master`
 * Gate (App\Providers\AppServiceProvider), the same check legacy's
 * can_manage_master() performs.
 *
 * `role_category`/`reviewer_department` are "privileged" types: legacy hides
 * them from non-Super-Admins entirely (list + edit + delete) and only ever
 * lets Super Admin delete them here — they're created/managed for real via
 * the dedicated Access Rights screen (AccessRightController), not this form.
 */
class MasterOptionController extends Controller
{
    public const BASE_TYPES = [
        'validation_category',
        'validation_scope',
        'machine_used',
        'product_type',
        'attachment_category',
    ];

    public const PRIVILEGED_TYPES = [
        'role_category',
        'reviewer_department',
    ];

    public function index(Request $request): Response
    {
        Gate::authorize('manage-master');

        $isSuperAdmin = $request->user()->isSuperAdmin();

        $options = MasterOption::query()
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->when(! $isSuperAdmin, fn ($query) => $query->whereNotIn('type', self::PRIVILEGED_TYPES))
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $editOption = null;
        if ($request->filled('edit') && ctype_digit((string) $request->query('edit'))) {
            $editOption = MasterOption::where('id', (int) $request->query('edit'))
                ->where('is_active', 1)
                ->first();

            if ($editOption && in_array($editOption->type, self::PRIVILEGED_TYPES, true) && ! $isSuperAdmin) {
                $editOption = null;
            }
        }

        return Inertia::render('admin/masters/index', [
            'options' => $options,
            'editOption' => $editOption,
            'types' => self::BASE_TYPES,
        ]);
    }

    public function store(StoreMasterOptionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $id = ! empty($data['id']) ? (int) $data['id'] : null;

        try {
            if ($id) {
                $option = MasterOption::find($id);
                if ($option) {
                    $option->type = $data['type'];
                    $option->name = $data['name'];
                    $option->sort_order = $data['sort_order'] ?? 0;
                    $option->is_active = true;
                    $option->save();
                }
            } else {
                $option = MasterOption::where('type', $data['type'])->where('name', $data['name'])->first()
                    ?? new MasterOption(['type' => $data['type'], 'name' => $data['name']]);
                $option->sort_order = $data['sort_order'] ?? 0;
                $option->is_active = true;
                $option->save();
            }
        } catch (QueryException $e) {
            return back()->withErrors(['name' => 'Master option dengan type dan name tersebut sudah ada.'])->withInput();
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $id ? 'Master option berhasil diperbarui.' : 'Master option berhasil ditambahkan.',
        ]);

        return to_route('admin.masters.index');
    }

    public function destroy(Request $request, MasterOption $masterOption): RedirectResponse
    {
        Gate::authorize('manage-master');

        if (in_array($masterOption->type, self::PRIVILEGED_TYPES, true) && ! $request->user()->isSuperAdmin()) {
            return back()->withErrors(['name' => 'Hanya Super Admin yang bisa menghapus role/reviewer master.']);
        }

        $masterOption->is_active = false;
        $masterOption->deleted_at = Carbon::now();
        $masterOption->deleted_by = $request->user()->id;
        $masterOption->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Master option berhasil dihapus.']);

        return to_route('admin.masters.index');
    }
}
