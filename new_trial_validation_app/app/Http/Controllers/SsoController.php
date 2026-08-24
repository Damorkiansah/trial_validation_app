<?php

namespace App\Http\Controllers;

use App\Models\SsoTicket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SsoController extends Controller
{
    /**
     * Consume an old-app -> new-app ticket and log the user in.
     */
    public function exchange(Request $request): RedirectResponse
    {
        $token = (string) $request->query('ticket', '');

        $claimed = $token !== '' && DB::table('sso_tickets')
            ->where('token', $token)
            ->where('direction', 'old_to_new')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['used_at' => now()]) === 1;

        $userId = $claimed ? SsoTicket::where('token', $token)->value('user_id') : null;
        $user = $userId !== null ? User::find((int) $userId) : null;

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi SSO tidak valid atau kedaluwarsa.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Issue a new-app -> old-app ticket for the current user and redirect.
     *
     * Manual test-trigger for the reverse SSO leg. Replace with real menu
     * links (calling this same issue logic) as modules migrate.
     */
    public function toOld(Request $request): RedirectResponse
    {
        $token = bin2hex(random_bytes(32));

        SsoTicket::create([
            'token' => $token,
            'user_id' => $request->user()->id,
            'direction' => 'new_to_old',
            'expires_at' => now()->addSeconds(30),
        ]);

        return redirect(rtrim(config('services.old_app.url'), '/').'/sso/consume?ticket='.$token);
    }
}
