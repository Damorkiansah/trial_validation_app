<?php

namespace App\Http\Controllers;

use App\Actions\Trials\SaveApprovalDecision;
use App\Http\Requests\Approvals\SaveApprovalDecisionRequest;
use App\Models\Trial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of legacy's /approvals (GET) + /trials/{id}/approval (POST) —
 * Manager QAC (or a specifically-assigned approver) making the final
 * Approve/Need Revision/Reject call on a Ready-for-Approval trial
 * (public/index.php:859-981).
 */
class ApprovalController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('view-approval-queue');

        $items = Trial::query()
            ->awaitingApprovalFor($request->user())
            ->with('approver:id,name,email')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('approvals/index', [
            'items' => $items,
        ]);
    }

    public function update(SaveApprovalDecisionRequest $request, int $trial, SaveApprovalDecision $action): RedirectResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        $action(
            $trial,
            $request->decision(),
            trim((string) $request->string('approval_comment')),
            $request->user(),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Keputusan approval berhasil disimpan.']);

        return to_route('approvals.index');
    }
}
