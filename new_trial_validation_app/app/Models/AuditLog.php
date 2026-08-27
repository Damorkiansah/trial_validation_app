<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Legacy `audit_logs` table — the old/new-diff audit trail written by
 * audit_log() in the legacy app's app/bootstrap.php. Distinct from
 * App\Models\ActivityLog (a simpler action/module log, ported in Fase 1) —
 * this one is the raw old_data/new_data JSON diff, and its only consumer in
 * this app is the Audit Print Log report (action='report_printed').
 *
 * @property int $id
 * @property int|null $trial_id
 * @property int|null $user_id
 * @property string|null $user_email
 * @property string $action
 * @property array<string, mixed>|null $old_data
 * @property array<string, mixed>|null $new_data
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 */
#[Fillable(['trial_id', 'user_id', 'user_email', 'action', 'old_data', 'new_data', 'ip_address', 'user_agent'])]
class AuditLog extends Model
{
    protected $table = 'audit_logs';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'old_data' => 'array',
            'new_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Trial, $this>
     */
    public function trial(): BelongsTo
    {
        return $this->belongsTo(Trial::class, 'trial_id');
    }
}
