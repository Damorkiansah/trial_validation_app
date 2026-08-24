# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository layout

This repo currently contains **two applications**:

- **Repo root** (`app/`, `public/`, `config/`, `database/`) — the **legacy production app**: plain PHP, no framework. This is what currently runs in production and must keep working throughout the migration.
- **`new_trial_validation_app/`** — the **new Laravel app** being built to replace it. It has its own `CLAUDE.md` with Laravel-specific commands and architecture notes — read that file when working inside that directory.
- **`MIGRATION_PLAN.md`** (repo root) — the full migration plan: target architecture, SSO bridge design between the two apps, API structure, and phase-by-phase rationale. This CLAUDE.md only summarizes it for quick reference; treat `MIGRATION_PLAN.md` as the source of truth for *why*, and update both files together when decisions change.

## Legacy app (repo root)

### Commands

There is no build step, package manager, or test suite for this app (no `composer.json`, no `phpunit`). It runs directly as plain PHP.

```powershell
# Run locally (from repo root) — uses whatever `php` is on PATH (Laragon's, on this machine)
php -S localhost:8000 -t public
# then open http://localhost:8000
```

Database setup: import `trial_validation_system.sql` first, then apply every file in `database/` in filename (date-prefixed) order — each is a standalone hardening/feature migration, there is no migration runner. See README.md "Upgrade hardening" for context on what each batch changed.

Default logins are listed in `README.md`.

### Architecture

Single front-controller pattern, no framework:

- `public/index.php` — the entire router. Every request comes in here (via `.htaccess` rewrite), gets normalized to a path with `normalize_request_path()`, and matched against a long sequence of `if($path==='...')` / `preg_match` blocks. There is no routing table — to find where a URL is handled, grep `public/index.php` for the literal path string.
- `app/bootstrap.php` — everything else: DB connection (`db()`, raw PDO, no ORM), session/auth helpers (`u()`, `role()`, `require_login()`), the **entire authorization model** (`is_admin()`, `is_staff()`, `is_reviewer()`, `can_edit()`, `can_approve_trial()`, `can_view_trial()`, `scoped_trials_parts()` for row-level visibility), CSRF (`csrf_token()`/`verify_csrf()`), audit logging (`audit_log()`, `logActivity()` → `audit_logs`/`activity_logs` tables), and domain logic like `trial_completeness()`. This file is the closest thing to a "service layer" in this codebase — read it before touching any workflow logic.
- `app/views/*.php` — views, rendered via `view($name, $data)` (wraps `layout.php`) or `partial($name, $data)`. Plain PHP templates, `h()` for HTML-escaping.
- Auth is native PHP session-based (`$_SESSION['user']`), sessions stored as files in `storage/sessions/`. Passwords are bcrypt (`password_verify`) — **compatible with Laravel's default `Hash::make`**, which the migration plan relies on.

Domain model: a "trial" (`trials_header`) moves through a state machine — `Draft → In Review → Ready for Approval → Approved` / `Rejected` / `Need Revision` — with per-department reviews (`trials_review`, keyed by `review_round`), weighing data (`trials_weighing`), parameter results (`trials_results`), and file attachments. Row-level visibility depends heavily on role + department + trial status (see `scoped_trials_parts()` and `can_view_trial()` in `bootstrap.php`) — this is the most complex/risky logic to port during migration.

## Migration status (summary — see `MIGRATION_PLAN.md` for full detail)

Target: Laravel + Inertia.js (React) on a new Ubuntu 26 server, migrated module-by-module (strangler pattern) while the legacy app keeps running, connected to the **same MySQL database**. A separate `/api/v1/*` (Sanctum Bearer token) is deferred until the mobile app project starts, after web migration completes.

**✅ Resolved 2026-08-24:** `new_trial_validation_app/` stays on `laravel/react-starter-kit` (**Inertia.js + React**, Fortify auth) instead of the plain API-only + separate Vite SPA + Sanctum originally described in `MIGRATION_PLAN.md` §2/§5 (draft is now stale on this point). Web pages are `Inertia::render()` responses under `routes/web.php`. `MIGRATION_PLAN.md` §2/§5 has been reconciled accordingly.

**✅ Fase 0 complete (2026-08-24).** **▶️ Start next session here:** continue Fase 1 (admin/master-data modules) below with Masters — Users, Products, Parameters, and Access Rights are done as of 2026-08-24; see their checklist entries for what's in scope vs deferred. Read `MIGRATION_PLAN.md` §4 for the SSO design (still applies as-is) and §6 Fase 0 for what the RBAC port produced (models, Policy, Gates) before building controllers that need to authorize against trials or admin-only actions. **Also read the 2026-08-24 shared-DB incident note under the Access Rights checklist item below before running any `migrate:fresh`/`migrate:refresh`/`db:wipe` or trusting a bare `--env=` flag in `new_trial_validation_app/`** — the DB was wiped once already this way and has no further backup behind it now.

### Fase 0 — Fondasi
- [x] Scaffold new Laravel app (`new_trial_validation_app/`, Laravel React Starter Kit)
- [x] Reconcile Inertia-based scaffold with MIGRATION_PLAN.md §2/§5 — resolved, keep Inertia (see above)
- [x] Implement `sso_tickets` table + old-app → new-app ticket issue/redirect (design: MIGRATION_PLAN.md §4)
- [x] Implement new-app → old-app ticket consume route (`/sso/consume` in legacy `index.php`)
- [x] End-to-end test of SSO bridge both directions — verified locally 2026-08-24 against the shared MySQL DB (both directions, plus replay/expiry/bogus-ticket rejection). Found and fixed a real bug in the process: Laravel's default app timezone (UTC) didn't match the local MySQL server's timezone (SE Asia Standard Time), so `sso_tickets.expires_at` written by Laravel's `now()` always looked already-expired to legacy's `NOW()`-based checks. Fixed via `new_trial_validation_app/config/app.php` timezone now reading `APP_TIMEZONE` (set to `Asia/Jakarta` locally) — **whichever server ends up hosting the shared MySQL DB (§8) must have its timezone matched by `APP_TIMEZONE`,** or this breaks again.
- [x] Port core RBAC from `bootstrap.php` into Laravel Policies — **done 2026-08-24.** `App\Models\User` got the role/department helpers (`isAdmin()`, `isStaff()`, `isReviewer()`, `reviewDepartmentsForUser()`, etc. — port of `is_admin()`/`is_staff()`/`is_reviewer()`/`reviewer_department_codes()`); new minimal model stubs `Trial`/`TrialReview`/`TrialEditPermission`/`MasterOption` map onto the shared `trials_header`/`trials_review`/`trial_edit_permissions`/`master_options` tables (Fase 3 will flesh these out further); `App\Policies\TrialPolicy` ports `can_view_trial()`/`can_edit()`/`can_approve_trial()`; `Trial::scopeVisibleTo()` ports the row-level list scoping from `scoped_trials_parts()`; simple admin-only checks (`can_manage_master()` etc.) became Gates in `AppServiceProvider`. This also required correcting `App\Models\User` to match the *real* shared `users` schema (`password_hash` not `password`, no `email_verified_at`/`remember_token`/`updated_at` columns) — the fresh-install migration branch (for sqlite/tests) and a few Fortify-adjacent controllers/actions (`SecurityController`, `ProfileController`, `ResetUserPassword`) were adjusted to match. Full test suite passes; spot-verified against real shared MySQL data via tinker. See `MIGRATION_PLAN.md` §6 Fase 0 for more detail.
- [x] Decide physical location of the shared MySQL DB (MIGRATION_PLAN.md §8) — **decided 2026-08-24: stays on the local/shared DB as-is for the whole migration.** The physical move + old→new data cutover happens at go-live (folded into Fase 4, "Turn off legacy PHP app" below), not before. The production-server timezone decision is deferred to that same point — not needed while everything's local.

### Fase 1 — Modul admin/master data (risiko rendah)
- [x] Users — **done 2026-08-24.** Ported the `/admin/users` + `/settings/users` block from legacy `public/index.php` (list+search, create/update-by-email upsert with password reset, soft delete) into `App\Http\Controllers\Admin\UserController` (`admin.users.index/store/destroy` in `routes/admin.php`) + `resources/js/pages/admin/users/index.tsx`. Authorization via new `App\Policies\UserPolicy` (viewAny/create/update/delete — the Super-Admin-target protection rules from legacy live here), following the `TrialPolicy` pattern from Fase 0. `App\Models\User::roleCategories()` ports `role_categories()`. **Deferred to a later pass** (separate legacy screen, Super-Admin-only, higher risk — ties into `trial_edit_permissions`): role-category master CRUD and the whole Access Rights screen (role/department reassignment, reviewer-department master, draft-trial edit-permission grant/revoke) at `/admin/access-rights` in legacy — tracked as the "Access Rights" item below. Found and fixed a real bug while building this: `User`'s `#[Fillable(['name','email','role','department'])]` attribute excludes `password_hash`/`is_active`/`deleted_at`/`deleted_by` by design, so mass-assignment helpers (`updateOrCreate`, `->update([...])`) silently dropped those fields — must set them via direct property assignment (`$user->password_hash = ...; $user->save()`) instead, as the controller now does. Also: `Carbon::now()` (not the `now()` helper) is needed when assigning to a `deleted_at`-typed property, since `AppServiceProvider` rebinds the `Date` facade to `CarbonImmutable` app-wide. Full Pest suite (36 tests incl. 9 new), Pint, Larastan, ESLint, tsc, and `npm run build` all pass.
- [x] Products — **done 2026-08-24.** Ported the `/admin/products` (`/templates/products`) block from legacy `public/index.php` (paginated list, edit-in-place via `?edit={id}`, create-or-update-by-id-or-name upsert, soft delete) into `App\Http\Controllers\Admin\ProductController` (`admin.products.index/store/destroy` in `routes/admin.php`) + `resources/js/pages/admin/products/index.tsx`. No new Policy needed — gated by the `manage-templates` Gate already defined in `AppServiceProvider` from the Fase 0 RBAC port (Admin or Staff role), matching legacy's `can_manage_templates()`. New `App\Models\Product` (`product_name`, `finish_good_code`, `is_active`, `deleted_at`, `deleted_by` — no timestamps, matches the real `products` schema) + a fresh-install migration mirroring the `users` table's pattern (`Schema::hasTable` guard, skipped on the shared MySQL DB). `finish_good_code` confirmed to be plain free-text with no auto-generation logic anywhere in the legacy app before porting. 10 new Pest tests (visibility gating, create, update-by-id, upsert-by-name, name-collision validation error, soft delete); full Pest suite (46 tests, 3 pre-existing unrelated skips), Pint, Larastan, ESLint, Prettier, tsc, and `npm run build` all pass.
- [x] Parameters — **done 2026-08-24.** Ported the `/admin/parameters` (`/templates/parameters`) block from legacy `public/index.php` (paginated list, edit-in-place via `?edit={id}`, create-or-update-by-id save — legacy never upserts by name here, only Products does — soft delete) into `App\Http\Controllers\Admin\ParameterController` (`admin.parameters.index/store/destroy` in `routes/admin.php`) + `resources/js/pages/admin/parameters/index.tsx`, added to the sidebar nav. Reuses the `manage-parameters` Gate already defined in `AppServiceProvider` from the Fase 0 RBAC port (Admin or Staff role), matching legacy's `can_manage_parameters()` exactly — no new Policy needed. New `App\Models\ValidationParameter` (`product_type`, `parameter_name`, `specification`, `sort_order`, `is_active`, `deleted_at`, `deleted_by` — no timestamps, matches the real `validation_parameters` schema) + a fresh-install migration following the `products` pattern. The product-type dropdown reads `master_options` (`type='product_type'`) via the existing `App\Models\MasterOption` stub — this **required adding a fresh-install migration for `master_options` too** (`2026_08_24_000003_create_master_options_table.php`), since no migration for that table existed yet even though `App\Models\User`'s role/department helpers already depended on it (they got away with it via a try/catch fallback to hardcoded defaults, added in the Fase 0 RBAC port — see `App\Models\User::reviewerDepartmentCodes()`/`roleCategories()`); this is schema-only, same shared-DB-skip-creation guard as the other tables, and does **not** front-run the separate Masters CRUD module below. Also added a new `Textarea` shadcn/ui component (`resources/js/components/ui/textarea.tsx`) — didn't exist yet in this scaffold. 9 new Pest tests (visibility gating, create, update-by-id, duplicate-name-always-creates-new since legacy has no name-uniqueness constraint here, validation errors, soft delete); full Pest suite (56 tests, 3 pre-existing unrelated skips), Pint, Larastan, ESLint, Prettier, tsc, and `npm run build` all pass. Spot-checked live against the real shared MySQL DB via `php artisan serve` + a scripted login (admin@local.test) — the page renders (200 OK, correct built JS asset referenced) with real product-type options from the shared DB.
- [x] Access Rights — **done 2026-08-24.** Ported the `/admin/access-rights` block from legacy `public/index.php` (user role/department reassignment, reviewer-department master CRUD, draft-trial edit-permission grant/revoke — all Super-Admin-only, `is_super_admin()` with no Admin fallback) into `App\Http\Controllers\Admin\AccessRightController` (`admin.access-rights.*` in `routes/admin.php`) + `resources/js/pages/admin/access-rights/index.tsx`, linked from the sidebar (Super Admin only). New `manage-access-rights` Gate in `AppServiceProvider`. Added fresh-install migrations for `trials_header` (minimal, matching the existing `Trial` model stub — none existed before this) and `trial_edit_permissions` (none existed before either); added a `unique(type,name)` index to the `master_options` fresh-install migration since no such constraint could be found in any legacy migration file despite legacy's upsert relying on one — the port doesn't depend on the DB constraint anyway (uses an app-level find-then-save, not `ON DUPLICATE KEY UPDATE`). 16 new Pest tests, full suite (72 tests, 3 pre-existing unrelated skips) + Pint + Larastan + ESLint + Prettier + tsc + `npm run build` all pass. Spot-checked live against the real shared MySQL DB (post-recovery, see incident note below) via `php artisan serve` + a scripted login as `admin@local.test` (promoted to Super Admin) — the page renders (200 OK) with real data: 42 users across 3 pages, 6 real Draft trials, 7 Staff users for the grant dropdown.

  **⚠️ Incident + recovery, 2026-08-24:** while sanity-checking the new migrations, an errant `php artisan migrate:fresh --env=testing` was run from `new_trial_validation_app/`. No `.env.testing` file exists in that project, so the flag silently fell back to the default `.env` — which points at this same shared MySQL DB (`trial_validation_system`), not an isolated test DB. `migrate:fresh` dropped every table and recreated only the ones with a Laravel migration, wiping everything else. **Restored the same day**, with the user's explicit approval at each step: the legacy app has no separate DB of its own (`config/database.php` points at the exact same shared DB, confirmed before assuming any recovery path), so the fix was importing `database/trial_validation_system.sql` — a fuller dump generated **2026-07-03** found alongside the smaller, staler root-level copy (generated 2026-05-21) — then reconciling schema against every file in `database/` (their `ADD COLUMN IF NOT EXISTS` syntax doesn't actually run on this MySQL 8.4.3 install, so each file's target columns were checked by hand; only `trials_header.approver_user_id` was genuinely missing and was added directly). `admin@local.test` was promoted from Admin to Super Admin (no Super Admin existed in the July dump) to spot-check the Super-Admin-only Access Rights screen. **Residual loss: the DB is now a 2026-07-03 snapshot, not current** — roughly 7 weeks of activity between then and the wipe is gone for good, and no fresher backup was found. Full detail in memory (`shared_db_wiped_2026_08_24` / `no_env_testing_flag_incident`). Going forward: never trust a bare `--env=` flag on a raw artisan command in `new_trial_validation_app/` without confirming the target `.env.<name>` file actually exists — there is no more spare backup behind this DB now.
- [ ] Masters
- [ ] Notifications
- [ ] Trash
- [ ] Activity Logs

### Fase 2 — Dashboard & Trials List (read-only)
- [ ] Dashboard
- [ ] Trials list (read-only)
- [ ] Decide print/PDF report approach in the new stack (needed before Fase 3 reports)

### Fase 3 — Inti workflow trial (paling besar & berisiko, dikerjakan paling akhir)
- [ ] Sub-tahapan dirinci saat fase ini dimulai (belum direncanakan detail — lihat MIGRATION_PLAN.md §8)
- [ ] Trial form (create/edit)
- [ ] Weighing
- [ ] Validation
- [ ] Review per departemen
- [ ] Approval (e-signature)
- [ ] Reports (approved / rejected / audit print log / department review / trial summary)
- [ ] Attachments/photo upload

### Fase 4 — Decommission
- [ ] Turn off legacy PHP app
- [ ] Remove SSO bridge (`/sso/*` routes both sides, `sso_tickets` table)
- [ ] Finish any deferred DB schema redesign
- [ ] Write API docs for the mobile app project
