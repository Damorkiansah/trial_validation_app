<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/notifications block in the legacy app's public/index.php:
 * a paginated, read-only-except-for-delete list of every notification in the
 * system (not scoped to the current user), with a permanent (hard) delete.
 * Gated by `manage-settings` (App\Providers\AppServiceProvider), the same
 * is_admin() check legacy performs here — no Staff fallback, unlike the
 * other Fase 1 modules.
 *
 * The per-user notification bell/inbox (`/notifications` in legacy) and its
 * `notification_user_status` read/removed tracking are out of scope; the
 * shared MySQL DB's `ON DELETE CASCADE` on that table's FK to `notifications`
 * handles cleanup of per-user status rows when a notification is deleted here.
 */
class NotificationController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('manage-settings');

        $notifications = Notification::query()
            ->with(['user:id,name,email', 'trial:id,trial_code'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/notifications/index', [
            'notifications' => $notifications,
        ]);
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        Gate::authorize('manage-settings');

        $notification->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Notification berhasil dihapus.']);

        return to_route('admin.notifications.index');
    }
}
