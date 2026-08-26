<?php

namespace App\Http\Controllers;

use App\Models\MasterOption;
use App\Models\Trial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /dashboard block in the legacy app's public/index.php
 * (lines 205-219): the current user's scoped trial list (Trial::scopeVisibleTo(),
 * Fase 0) plus summary counts (Trial::summaryCounts()) and the same search
 * filters used by the trials-list pages (Trial::scopeSearch()).
 *
 * Fase 3 adds the "New Trial" button back (legacy's, and per-row Action
 * column now that trials.create/edit exist) — see TrialController.
 */
class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'product_type' => trim((string) $request->query('product_type', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
            'status' => trim((string) $request->query('status', '')),
        ];

        $trials = Trial::query()
            ->visibleTo($user)
            ->search($filters)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $trials->getCollection()->each(function (Trial $trial) use ($user) {
            $trial->setAttribute('can_edit', Gate::forUser($user)->allows('update', $trial));
        });

        $summary = Trial::summaryCounts($user);

        $productTypes = MasterOption::query()
            ->where('type', 'product_type')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        return Inertia::render('dashboard', [
            'trials' => $trials,
            'filters' => $filters,
            'productTypes' => $productTypes,
            'summary' => $summary,
            'canCreateTrial' => Gate::forUser($user)->allows('create', Trial::class),
        ]);
    }
}
