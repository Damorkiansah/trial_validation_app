<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Legacy `activity_logs` table — a system-wide audit trail of actions
 * (LOGIN/LOGOUT, CREATE/UPDATE/DELETE, ...) written by logActivity() in the
 * legacy app's app/bootstrap.php. Read-only + delete for the admin screen
 * (see App\Http\Controllers\Admin\ActivityLogController); nothing in this
 * app writes to it yet since no trial workflow has been ported (Fase 3).
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $user_name
 * @property string|null $user_role
 * @property string $action
 * @property string $module
 * @property string|null $record_id
 * @property string|null $record_label
 * @property string|null $old_data
 * @property string|null $new_data
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 */
#[Fillable([
    'user_id', 'user_name', 'user_role', 'action', 'module',
    'record_id', 'record_label', 'old_data', 'new_data', 'ip_address', 'user_agent',
])]
class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
