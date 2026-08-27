<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Gates for capabilities that aren't tied to a specific model instance
     * (no Policy subject to attach them to yet). Ported from the legacy
     * app's app/bootstrap.php: can_manage_settings(), can_manage_master(),
     * can_manage_parameters(), can_view_products_template(),
     * can_manage_templates(). Fase 1 (Products/Parameters/Masters modules)
     * may replace these with proper model Policies once those models exist.
     */
    protected function configureGates(): void
    {
        Gate::define('manage-settings', fn (User $user) => $user->isAdmin());
        Gate::define('manage-master', fn (User $user) => $user->isAdmin() || $user->role === 'Staff');
        Gate::define('manage-parameters', fn (User $user) => $user->isAdmin() || $user->role === 'Staff');
        Gate::define('view-products-template', fn (User $user) => $user->isAdmin() || $user->role === 'Staff');
        Gate::define('manage-templates', fn (User $user) => $user->isAdmin() || $user->role === 'Staff');
        // Port of the `is_super_admin()` check guarding legacy's
        // /admin/access-rights screen (role/department reassignment,
        // reviewer-department master, draft-trial edit-permission
        // grant/revoke) — Super Admin only, no Admin fallback.
        Gate::define('manage-access-rights', fn (User $user) => $user->isSuperAdmin());
        // Port of can_approve_trials() guarding legacy's /approvals queue —
        // who may open the approval queue at all (User::canApproveTrials()
        // already existed from the Fase 0 RBAC port, unused until now).
        Gate::define('view-approval-queue', fn (User $user) => $user->canApproveTrials());
    }
}
