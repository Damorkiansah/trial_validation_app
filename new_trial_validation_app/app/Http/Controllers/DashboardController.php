<?php

namespace App\Http\Controllers;

use App\Models\MasterOption;
use App\Models\Trial;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /dashboard block in the legacy app's public/index.php
 * (lines 205-219): the current user's scoped trial list (Trial::scopeVisibleTo(),
 * Fase 0) plus summary counts (Trial::summaryCounts()) and the same search
 * filters used by the trials-list pages (Trial::scopeSearch()).
 *
 * Read-only by design (Fase 2) — legacy's "New Trial" button and per-row
 * Action column both link to pages that don't exist yet (Fase 3), so neither
 * is ported here.
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
        ]);
    }
}
