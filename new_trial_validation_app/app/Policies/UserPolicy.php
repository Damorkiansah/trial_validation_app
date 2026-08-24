<?php

namespace App\Policies;

use App\Models\User;

/**
 * Port of the admin-only authorization checks around /admin/users in the
 * legacy app's public/index.php: is_admin() gates the module, with an extra
 * Super-Admin-only guard on creating/editing/deleting a Super Admin account.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        return $target->role !== 'Super Admin' || $user->isSuperAdmin();
    }

    public function delete(User $user, User $target): bool
    {
        if (! $user->isAdmin() || $user->id === $target->id) {
            return false;
        }

        return $target->role !== 'Super Admin' || $user->isSuperAdmin();
    }
}
