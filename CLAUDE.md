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

**✅ Fase 0 complete (2026-08-24).** **▶️ Start next session here:** begin Fase 1 (admin/master-data modules) below — Users is a reasonable first module since `App\Models\User` already exists and has the role/department RBAC helpers from Fase 0. Read `MIGRATION_PLAN.md` §4 for the SSO design (still applies as-is) and §6 Fase 0 for what the RBAC port actually produced (models, Policy, Gates) before building controllers that need to authorize against trials or admin-only actions.

### Fase 0 — Fondasi
- [x] Scaffold new Laravel app (`new_trial_validation_app/`, Laravel React Starter Kit)
- [x] Reconcile Inertia-based scaffold with MIGRATION_PLAN.md §2/§5 — resolved, keep Inertia (see above)
- [x] Implement `sso_tickets` table + old-app → new-app ticket issue/redirect (design: MIGRATION_PLAN.md §4)
- [x] Implement new-app → old-app ticket consume route (`/sso/consume` in legacy `index.php`)
- [x] End-to-end test of SSO bridge both directions — verified locally 2026-08-24 against the shared MySQL DB (both directions, plus replay/expiry/bogus-ticket rejection). Found and fixed a real bug in the process: Laravel's default app timezone (UTC) didn't match the local MySQL server's timezone (SE Asia Standard Time), so `sso_tickets.expires_at` written by Laravel's `now()` always looked already-expired to legacy's `NOW()`-based checks. Fixed via `new_trial_validation_app/config/app.php` timezone now reading `APP_TIMEZONE` (set to `Asia/Jakarta` locally) — **whichever server ends up hosting the shared MySQL DB (§8) must have its timezone matched by `APP_TIMEZONE`,** or this breaks again.
- [x] Port core RBAC from `bootstrap.php` into Laravel Policies — **done 2026-08-24.** `App\Models\User` got the role/department helpers (`isAdmin()`, `isStaff()`, `isReviewer()`, `reviewDepartmentsForUser()`, etc. — port of `is_admin()`/`is_staff()`/`is_reviewer()`/`reviewer_department_codes()`); new minimal model stubs `Trial`/`TrialReview`/`TrialEditPermission`/`MasterOption` map onto the shared `trials_header`/`trials_review`/`trial_edit_permissions`/`master_options` tables (Fase 3 will flesh these out further); `App\Policies\TrialPolicy` ports `can_view_trial()`/`can_edit()`/`can_approve_trial()`; `Trial::scopeVisibleTo()` ports the row-level list scoping from `scoped_trials_parts()`; simple admin-only checks (`can_manage_master()` etc.) became Gates in `AppServiceProvider`. This also required correcting `App\Models\User` to match the *real* shared `users` schema (`password_hash` not `password`, no `email_verified_at`/`remember_token`/`updated_at` columns) — the fresh-install migration branch (for sqlite/tests) and a few Fortify-adjacent controllers/actions (`SecurityController`, `ProfileController`, `ResetUserPassword`) were adjusted to match. Full test suite passes; spot-verified against real shared MySQL data via tinker. See `MIGRATION_PLAN.md` §6 Fase 0 for more detail.
- [x] Decide physical location of the shared MySQL DB (MIGRATION_PLAN.md §8) — **decided 2026-08-24: stays on the local/shared DB as-is for the whole migration.** The physical move + old→new data cutover happens at go-live (folded into Fase 4, "Turn off legacy PHP app" below), not before. The production-server timezone decision is deferred to that same point — not needed while everything's local.

### Fase 1 — Modul admin/master data (risiko rendah)
- [ ] Users
- [ ] Products
- [ ] Parameters
- [ ] Access Rights
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
