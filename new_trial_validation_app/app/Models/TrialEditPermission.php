<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Legacy `trial_edit_permissions` table — per-user, per-trial grants that
 * let a non-owner edit a Draft trial (see App\Models\User::hasTrialEditPermission()
 * and App\Policies\TrialPolicy::update()).
 *
 * @property int $id
 * @property int $trial_id
 * @property int $user_id
 * @property bool $can_edit
 * @property int|null $granted_by
 * @property Carbon $granted_at
 * @property int|null $revoked_by
 * @property Carbon|null $revoked_at
 */
#[Fillable(['trial_id', 'user_id', 'can_edit', 'granted_by', 'granted_at', 'revoked_by', 'revoked_at'])]
class TrialEditPermission extends Model
{
    protected $table = 'trial_edit_permissions';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'can_edit' => 'boolean',
            'granted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Trial, $this>
     */
    public function trial(): BelongsTo
    {
        return $this->belongsTo(Trial::class, 'trial_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
