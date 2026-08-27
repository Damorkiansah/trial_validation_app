<?php

namespace App\Policies;

use App\Models\Trial;
use App\Models\TrialReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Port of the trial-related authorization checks in the legacy app's
 * app/bootstrap.php: can_view_trial(), can_edit(), can_approve_trial().
 * Trial::scopeVisibleTo() is the list-level counterpart (scoped_trials_parts()).
 */
class TrialPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Trial $trial): bool
    {
        if ($trial->progress_status === 'Draft') {
            return $user->isSuperAdmin()
                || $user->isTrialOwner($trial)
                || $user->hasTrialEditPermission($trial->id);
        }

        if ($user->isViewer()) {
            return true;
        }

        $approverRoles = ['Manager QAC', 'Team Leader', 'Part Leader', 'Team Leader QA'];
        if ($user->isStaff() || in_array($user->role, $approverRoles, true)) {
            return true;
        }

        if ($user->isReviewer()) {
            $departments = $user->reviewDepartmentsForUser();
            $round = $trial->currentReviewRound();

            return TrialReview::query()
                ->where('trial_id', $trial->id)
                ->whereIn(DB::raw('UPPER(TRIM(department))'), $departments)
                ->when(
                    in_array($trial->progress_status, ['In Review', 'Ready for Approval'], true),
                    fn ($q) => $q->where('review_round', $round)
                )
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Trial $trial): bool
    {
        if (! $user->isStaff() || ! in_array($trial->progress_status, ['Draft', 'Need Revision'], true)) {
            return false;
        }

        if ($trial->progress_status === 'Draft') {
            return $user->isTrialOwner($trial) || $user->hasTrialEditPermission($trial->id);
        }

        return true;
    }

    /**
     * Port of the authorization checks inline in legacy's POST
     * /trials/{id}/approval handler (public/index.php:884-898). Note this is
     * narrower than the approval *queue's* visibility (see
     * Trial::scopeAwaitingApprovalFor()): Team Leader/Part Leader/Team Leader
     * QA can *see* every Ready-for-Approval trial in the queue, but — same as
     * every other non-Admin/Manager-QAC role — can only actually approve one
     * that's specifically assigned to them via approver_user_id.
     */
    public function approve(User $user, Trial $trial): bool
    {
        if ($user->isAdmin() || $user->isManagerQac()) {
            return true;
        }

        if (! empty($trial->approver_user_id)) {
            return (int) $trial->approver_user_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Trial $trial): bool
    {
        return $user->isAdmin();
    }
}
