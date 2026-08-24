<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Maps onto the legacy `users` table, shared with the old PHP app (see
 * ../../../CLAUDE.md and MIGRATION_PLAN.md §4). Schema is
 * id/name/email/password_hash/role/department/is_active/created_at/deleted_at/deleted_by
 * — no `password`, `updated_at`, `email_verified_at`, or `remember_token`
 * columns, so several Authenticatable defaults are overridden below.
 *
 * Role/department helpers here are a port of the authorization primitives in
 * the legacy app's app/bootstrap.php (is_admin(), is_staff(), is_reviewer(),
 * reviewer_department_codes(), etc.) — see App\Policies\TrialPolicy and
 * Trial::scopeVisibleTo() for where they're used.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $role
 * @property string|null $department
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $deleted_at
 * @property int|null $deleted_by
 */
#[Fillable(['name', 'email', 'role', 'department'])]
#[Hidden(['password_hash'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    /**
     * Legacy `users` table has no remember_token column.
     */
    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void {}

    public function getRememberTokenName(): string
    {
        return '';
    }

    public static function normalizeDepartment(?string $dept): string
    {
        $dept = strtoupper(trim((string) $dept));

        return preg_replace('/\s+/', ' ', $dept) ?? '';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'Super Admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin' || $this->isSuperAdmin();
    }

    public function isStaff(): bool
    {
        return $this->role === 'Staff' || $this->isAdmin();
    }

    public function isManagerQac(): bool
    {
        return $this->role === 'Manager QAC';
    }

    public function isViewer(): bool
    {
        return $this->role === 'Viewer';
    }

    public function departmentCode(): string
    {
        $dept = self::normalizeDepartment($this->department ?? '');

        return $dept !== '' ? $dept : self::normalizeDepartment($this->role);
    }

    /**
     * Department codes eligible to be a per-department reviewer, i.e. the
     * hardcoded defaults plus anything added via master_options
     * (type=reviewer_department). Falls back to defaults if that table
     * can't be queried (matches legacy bootstrap.php behavior).
     *
     * @return list<string>
     */
    public static function reviewerDepartmentCodes(): array
    {
        $defaults = ['PRD', 'RNI', 'QAC', 'PRNI', 'PI'];

        try {
            $codes = $defaults;
            $names = MasterOption::query()
                ->where('type', 'reviewer_department')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name');

            foreach ($names as $name) {
                $code = self::normalizeDepartment($name);
                if ($code !== '' && ! in_array($code, $codes, true)) {
                    $codes[] = $code;
                }
            }

            return $codes;
        } catch (\Throwable) {
            return $defaults;
        }
    }

    /**
     * Department codes this user reviews for (their role and/or their
     * department, when either is a recognized reviewer department).
     *
     * @return list<string>
     */
    public function reviewDepartmentsForUser(): array
    {
        $codes = self::reviewerDepartmentCodes();
        $items = [];
        $role = self::normalizeDepartment($this->role);
        $dept = $this->departmentCode();
        if (in_array($role, $codes, true)) {
            $items[] = $role;
        }
        if (in_array($dept, $codes, true) && ! in_array($dept, $items, true)) {
            $items[] = $dept;
        }

        return array_values(array_unique($items));
    }

    public function isReviewer(): bool
    {
        return ! $this->isManagerQac() && count($this->reviewDepartmentsForUser()) > 0;
    }

    /**
     * Assignable role categories: the hardcoded defaults, plus any reviewer
     * department codes and any custom roles added via master_options
     * (type=role_category). Port of legacy bootstrap.php's role_categories().
     *
     * @return list<string>
     */
    public static function roleCategories(): array
    {
        $roles = ['Staff', 'Viewer', 'Manager QAC', 'Admin', 'Super Admin'];

        foreach (self::reviewerDepartmentCodes() as $dept) {
            if (! in_array($dept, $roles, true)) {
                $roles[] = $dept;
            }
        }

        try {
            $names = MasterOption::query()
                ->where('type', 'role_category')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name');

            foreach ($names as $name) {
                $name = trim((string) $name);
                if ($name !== '' && ! in_array($name, $roles, true)) {
                    $roles[] = $name;
                }
            }
        } catch (\Throwable) {
            // fall through with defaults, matching legacy behavior
        }

        return $roles;
    }

    public function roleLabel(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Super Admin';
        }
        if ($this->isAdmin()) {
            return 'Admin';
        }
        if ($this->role === 'Staff') {
            return 'Staff Trial';
        }
        if ($this->isViewer()) {
            return 'Viewer';
        }
        if ($this->isReviewer()) {
            return 'Reviewer';
        }
        if ($this->isManagerQac()) {
            return 'Manager QAC';
        }

        return $this->role;
    }

    public function isTrialOwner(Trial $trial): bool
    {
        return trim((string) $trial->created_by) !== ''
            && strcasecmp(trim((string) $trial->created_by), trim((string) $this->email)) === 0;
    }

    public function hasTrialEditPermission(int $trialId): bool
    {
        if (! $trialId) {
            return false;
        }

        return TrialEditPermission::query()
            ->where('trial_id', $trialId)
            ->where('user_id', $this->id)
            ->where('can_edit', 1)
            ->whereNull('revoked_at')
            ->exists();
    }

    public function hasAssignedApproval(): bool
    {
        return Trial::query()
            ->where('progress_status', 'Ready for Approval')
            ->whereNull('deleted_at')
            ->where('approver_user_id', $this->id)
            ->exists();
    }

    /**
     * Whether this user can approve trials in general (used to gate the
     * approval queue / list scoping) — as opposed to Policy::approve(),
     * which checks a specific trial.
     */
    public function canApproveTrials(): bool
    {
        if ($this->isAdmin() || $this->isManagerQac()) {
            return true;
        }

        $approverRoles = ['Team Leader', 'Part Leader', 'Team Leader QA'];
        if (in_array($this->role, $approverRoles, true)) {
            return true;
        }

        return $this->hasAssignedApproval();
    }
}
