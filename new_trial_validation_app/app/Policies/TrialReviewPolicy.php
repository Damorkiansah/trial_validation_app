<?php

namespace App\Policies;

use App\Models\TrialReview;
use App\Models\User;

/**
 * Port of the authorization checks inline in the legacy app's public/index.php
 * for /reviews (die('Reviewer only')) and /review/{id}/save (department match
 * + "is this review still the active one" check, public/index.php:796-818).
 */
class TrialReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isReviewer();
    }

    public function update(User $user, TrialReview $review): bool
    {
        if (! $user->isReviewer()) {
            return false;
        }

        if (! in_array(User::normalizeDepartment($review->department), $user->reviewDepartmentsForUser(), true)) {
            return false;
        }

        $trial = $review->trial;

        if (! $trial) {
            return false;
        }

        return $trial->progress_status === 'In Review'
            && (int) $review->review_round === $trial->currentReviewRound()
            && $review->status === 'Pending';
    }
}
