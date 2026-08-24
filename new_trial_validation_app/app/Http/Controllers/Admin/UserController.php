<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/users (and /settings/users) block in the legacy app's
 * public/index.php (list, save/upsert-by-email, soft-delete). Role-category
 * master data and the separate Access Rights screen are ported later.
 */
class UserController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);

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

        $roleCategories = User::roleCategories();
        $hasSuperAdmin = User::where('role', 'Super Admin')->where('is_active', 1)->whereNull('deleted_at')->exists();
        if (! $request->user()->isSuperAdmin() && $hasSuperAdmin) {
            $roleCategories = array_values(array_filter($roleCategories, fn ($role) => $role !== 'Super Admin'));
        }

        return Inertia::render('admin/users/index', [
            'users' => $users,
            'roleCategories' => $roleCategories,
            'filters' => ['q' => $search],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        $department = $data['department'];

        $hasSuperAdmin = User::where('role', 'Super Admin')->where('is_active', 1)->whereNull('deleted_at')->exists();
        if ($role === 'Super Admin' && ! $request->user()->isSuperAdmin() && $hasSuperAdmin) {
            return back()->withErrors(['role' => 'Hanya Super Admin yang bisa membuat atau memberikan role Super Admin.'])->withInput();
        }

        if (in_array(User::normalizeDepartment($role), User::reviewerDepartmentCodes(), true)) {
            $role = User::normalizeDepartment($role);
            $department = $role;
        } elseif ($department === '') {
            $department = User::normalizeDepartment($role);
        }

        $existing = User::where('email', $data['email'])->first();
        if ($existing) {
            Gate::authorize('update', $existing);
        } else {
            Gate::authorize('create', User::class);
        }

        // Set attributes directly rather than mass-assigning: password_hash,
        // is_active, deleted_at and deleted_by are intentionally excluded
        // from User's #[Fillable] list since they shouldn't be settable from
        // arbitrary request input.
        $user = $existing ?? new User(['email' => $data['email']]);
        $user->name = $data['name'];
        $user->role = $role;
        $user->department = $department;
        $user->password_hash = Hash::make($data['password']);
        $user->is_active = true;
        $user->deleted_at = null;
        $user->deleted_by = null;
        $user->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $existing ? 'User berhasil diperbarui.' : 'User berhasil dibuat.',
        ]);

        return to_route('admin.users.index');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $user->is_active = false;
        $user->deleted_at = Carbon::now();
        $user->deleted_by = $request->user()->id;
        $user->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'User berhasil dinonaktifkan.']);

        return to_route('admin.users.index');
    }
}
