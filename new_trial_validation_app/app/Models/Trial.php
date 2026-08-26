<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Legacy `trials_header` table — the core "trial" record. Minimal stub for
 * now (see App\Policies\TrialPolicy for the row-level authorization this
 * exists to support); Fase 3 (Inti workflow trial) will flesh out the rest
 * of the workflow (weighing, results, attachments, reviews).
 *
 * @property int $id
 * @property string $trial_code
 * @property int|null $product_id
 * @property string $product_name
 * @property string $product_type
 * @property string $progress_status
 * @property string|null $final_decision
 * @property int $revision_no
 * @property int|null $approver_user_id
 * @property string|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $deleted_by
 */
#[Fillable([
    'trial_code', 'product_id', 'product_name', 'finish_good_code', 'product_type',
    'validation_date', 'validation_category', 'risk_level', 'validation_scope',
    'machine_used', 'estimate_qty', 'batch_number', 'bulk_code', 'support_team',
    'initiated_person_team', 'reason', 'bom', 'current_step', 'progress_status',
    'pending_with', 'final_decision', 'revision_no', 'approved_by', 'approved_at',
    'rejected_by', 'rejected_at', 'approval_comment', 'approver_user_id', 'created_by',
])]
class Trial extends Model
{
    protected $table = 'trials_header';

    const UPDATED_AT = 'updated_at';

    const CREATED_AT = 'created_at';

    protected function casts(): array
    {
        return [
            'validation_date' => 'date',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<TrialReview, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(TrialReview::class, 'trial_id');
    }

    /**
     * @return HasMany<TrialEditPermission, $this>
     */
    public function editPermissions(): HasMany
    {
        return $this->hasMany(TrialEditPermission::class, 'trial_id');
    }

    /**
     * Named `deletedByUser` (not `deletedBy`) so JSON serialization keys it
     * as `deleted_by_user` — Eloquent's toArray() otherwise collides a
     * loaded relation's snake_case key with the raw `deleted_by` column
     * (foreign key id) of the same name, and the relation would silently
     * win, hiding the id from any consumer that still wants it.
     *
     * @return BelongsTo<User, $this>
     */
    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Matches legacy admin_trash's `u_creator.email=h.created_by` join —
     * `created_by` stores the creating user's email, not their id.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'email');
    }

    public function currentReviewRound(): int
    {
        return (int) $this->revision_no + 1;
    }

    /**
     * Row-level visibility scope — port of scoped_trials_parts() in the
     * legacy app/bootstrap.php. Restricts $query to trials $user is allowed
     * to see at all; App\Policies\TrialPolicy::view() makes the same call
     * for a single already-loaded trial.
     *
     * $statusGroup mirrors the legacy status-group filters used by list
     * pages (approved/in-review/need-revision/rejected/waiting/draft).
     * List-page search filters (q, product_type, etc.) are NOT part of this
     * scope — those aren't authorization, they belong in the controller
     * that builds the trials list (Fase 2).
     *
     * @param  Builder<Trial>  $query
     * @return Builder<Trial>
     */
    public function scopeVisibleTo(Builder $query, User $user, ?string $statusGroup = null): Builder
    {
        if ($user->isReviewer() && ! $user->isStaff() && ! $user->canApproveTrials()) {
            $departments = $user->reviewDepartmentsForUser();

            $query->join('trials_review as tr_scope', 'tr_scope.trial_id', '=', 'trials_header.id')
                ->whereIn(DB::raw('UPPER(TRIM(tr_scope.department))'), $departments)
                ->where(function (Builder $q) {
                    $q->whereNotIn('trials_header.progress_status', ['In Review', 'Ready for Approval'])
                        ->orWhereRaw('tr_scope.review_round = trials_header.revision_no + 1');
                })
                ->select('trials_header.*')
                ->distinct();
        }

        $query->whereNull('trials_header.deleted_at');

        if (! $user->isSuperAdmin()) {
            $email = strtolower(trim($user->email));

            $query->where(function (Builder $q) use ($user, $email) {
                $q->where('trials_header.progress_status', '!=', 'Draft')
                    ->orWhereRaw('LOWER(TRIM(trials_header.created_by)) = ?', [$email])
                    ->orWhereExists(function ($sub) use ($user) {
                        $sub->select(DB::raw(1))
                            ->from('trial_edit_permissions as tep')
                            ->whereColumn('tep.trial_id', 'trials_header.id')
                            ->where('tep.user_id', $user->id)
                            ->where('tep.can_edit', 1)
                            ->whereNull('tep.revoked_at');
                    });
            });
        }

        match ($statusGroup) {
            'approved' => $query->where('trials_header.progress_status', 'Approved'),
            'in-review' => $query->where('trials_header.progress_status', 'In Review'),
            'need-revision' => $query->where('trials_header.progress_status', 'Need Revision'),
            'rejected' => $query->where(fn (Builder $q) => $q->where('trials_header.progress_status', 'Rejected')
                ->orWhere('trials_header.final_decision', 'Rejected')),
            'waiting' => $query->where('trials_header.progress_status', 'Ready for Approval'),
            'draft' => $query->where('trials_header.progress_status', 'Draft'),
            default => null,
        };

        if ($statusGroup === 'waiting' && ! $user->isAdmin() && ! $user->isManagerQac()) {
            $query->where('trials_header.approver_user_id', $user->id);
        } elseif ($statusGroup === 'waiting' && ! $user->isAdmin()) {
            $query->where(fn (Builder $q) => $q->whereNull('trials_header.approver_user_id')
                ->orWhere('trials_header.approver_user_id', $user->id));
        }

        return $query;
    }
}
