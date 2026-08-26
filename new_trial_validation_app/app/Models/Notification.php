<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Legacy `notifications` table — in-app notifications targeted at a user,
 * role, or department, optionally tied to a trial. Minimal stub for the
 * admin notifications screen (list + hard delete, see
 * App\Http\Controllers\Admin\NotificationController); the per-user
 * notification bell/inbox (`/notifications` in legacy, with its
 * `notification_user_status` read/removed tracking) is out of scope for now.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $role_target
 * @property string|null $department_target
 * @property int|null $trial_id
 * @property string $title
 * @property string $message
 * @property string $type
 * @property bool $is_read
 * @property Carbon|null $read_at
 * @property bool $removed_by_user
 * @property Carbon|null $removed_at
 * @property Carbon|null $created_at
 */
#[Fillable(['user_id', 'role_target', 'department_target', 'trial_id', 'title', 'message', 'type', 'is_read', 'read_at'])]
class Notification extends Model
{
    protected $table = 'notifications';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'removed_by_user' => 'boolean',
            'removed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Trial, $this>
     */
    public function trial(): BelongsTo
    {
        return $this->belongsTo(Trial::class, 'trial_id');
    }
}
