<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrantDraftPermissionRequest;
use App\Http\Requests\Admin\StoreReviewerDepartmentRequest;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\MasterOption;
use App\Models\Trial;
use App\Models\TrialEditPermission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/access-rights block in the legacy app's
 * public/index.php: user role/department reassignment, reviewer-department
 * master CRUD, and draft-trial edit-permission grant/revoke. Unlike Users/
 * Products/Parameters, every action here is Super-Admin-only (legacy's
 * is_super_admin() check, no Admin fallback) — see the `manage-access-rights`
 * Gate in App\Providers\AppServiceProvider.
 */
class AccessRightController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('manage-access-rights');

        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->whereNull('deleted_at')
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";
                $query->where(function ($q) use ($like) {
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('role', 'like', $like)
                        ->orWhere('department', 'like', $like);
                });
            })
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $editUser = null;
        if ($request->filled('edit') && ctype_digit((string) $request->query('edit'))) {
            $editUser = User::where('id', (int) $request->query('edit'))
                ->whereNull('deleted_at')
                ->first();
        }

        $reviewerDepartments = MasterOption::query()
            ->where('type', 'reviewer_department')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'sort_order']);

        $draftTrials = Trial::query()
            ->where('progress_status', 'Draft')
            ->whereNull('deleted_at')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'trial_code', 'product_name', 'created_by', 'created_at']);

        $staffUsers = User::query()
            ->where('role', 'Staff')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->orderBy('email')
            ->get(['id', 'name', 'email']);

        $draftPermissions = TrialEditPermission::query()
            ->with(['trial:id,trial_code,product_name,created_by', 'user:id,name,email', 'grantedBy:id,name,email'])
            ->where('can_edit', 1)
            ->whereNull('revoked_at')
            ->whereHas('trial', fn ($q) => $q->whereNull('deleted_at'))
            ->orderByDesc('granted_at')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('admin/access-rights/index', [
            'users' => $users,
            'filters' => ['q' => $search],
            'editUser' => $editUser,
            'roleCategories' => User::roleCategories(),
            'reviewerDepartments' => $reviewerDepartments,
            'draftTrials' => $draftTrials,
            'staffUsers' => $staffUsers,
            'draftPermissions' => $draftPermissions,
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['role' => 'Tidak bisa mengubah hak akses akun sendiri dari halaman ini.']);
        }

        $data = $request->validated();
        $role = trim($data['role']);
        $department = User::normalizeDepartment($data['department'] ?? '');

        if (! in_array($role, User::roleCategories(), true)) {
            return back()->withErrors(['role' => 'Kategori hak akses tidak valid.']);
        }

        if (in_array(User::normalizeDepartment($role), User::reviewerDepartmentCodes(), true)) {
            $role = User::normalizeDepartment($role);
            $department = $role;
        } elseif ($department === '') {
            $department = User::normalizeDepartment($role);
        }

        $user->role = $role;
        $user->department = $department;
        $user->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Hak akses user berhasil diperbarui.']);

        return to_route('admin.access-rights.index');
    }

    public function storeReviewerDepartment(StoreReviewerDepartmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $name = User::normalizeDepartment($data['name']);

        if ($name === '') {
            return back()->withErrors(['name' => 'Nama kategori reviewer wajib diisi.']);
        }

        $option = MasterOption::firstWhere(['type' => 'reviewer_department', 'name' => $name])
            ?? new MasterOption(['type' => 'reviewer_department', 'name' => $name]);
        $option->sort_order = (int) ($data['sort_order'] ?? 0);
        $option->is_active = true;
        $option->deleted_at = null;
        $option->deleted_by = null;
        $option->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kategori reviewer berhasil disimpan.']);

        return to_route('admin.access-rights.index');
    }

    public function destroyReviewerDepartment(Request $request, MasterOption $reviewerDepartment): RedirectResponse
    {
        Gate::authorize('manage-access-rights');
        abort_if($reviewerDepartment->type !== 'reviewer_department', 404);

        $reviewerDepartment->is_active = false;
        $reviewerDepartment->deleted_at = Carbon::now();
        $reviewerDepartment->deleted_by = $request->user()->id;
        $reviewerDepartment->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kategori reviewer berhasil dihapus.']);

        return to_route('admin.access-rights.index');
    }

    public function grantPermission(GrantDraftPermissionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $trial = Trial::query()
            ->where('id', $data['trial_id'])
            ->where('progress_status', 'Draft')
            ->whereNull('deleted_at')
            ->first();

        $targetUser = User::query()
            ->where('id', $data['user_id'])
            ->where('role', 'Staff')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (! $trial || ! $targetUser) {
            return back()->withErrors(['trial_id' => 'Draft report atau user Staff tidak valid.']);
        }

        if (strcasecmp(trim((string) $trial->created_by), trim($targetUser->email)) === 0) {
            return back()->withErrors(['user_id' => 'Owner sudah memiliki akses edit Draft report tersebut.']);
        }

        $permission = TrialEditPermission::firstOrNew(['trial_id' => $trial->id, 'user_id' => $targetUser->id]);
        $permission->can_edit = true;
        $permission->granted_by = $request->user()->id;
        $permission->granted_at = Carbon::now();
        $permission->revoked_by = null;
        $permission->revoked_at = null;
        $permission->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Izin edit Draft report berhasil diberikan.']);

        return to_route('admin.access-rights.index');
    }

    public function revokePermission(Request $request, TrialEditPermission $permission): RedirectResponse
    {
        Gate::authorize('manage-access-rights');

        if (is_null($permission->revoked_at)) {
            $permission->can_edit = false;
            $permission->revoked_by = $request->user()->id;
            $permission->revoked_at = Carbon::now();
            $permission->save();

            Inertia::flash('toast', ['type' => 'success', 'message' => 'Izin edit Draft report berhasil dicabut.']);
        }

        return to_route('admin.access-rights.index');
    }
}
