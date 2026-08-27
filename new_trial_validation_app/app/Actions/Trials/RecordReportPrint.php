<?php

namespace App\Actions\Trials;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\Trial;
use App\Models\User;

/**
 * Port of the `report_printed` slice of legacy's audit_log() plus its
 * logActivity('PRINT_REPORT','REPORT',...) call (public/index.php:330-337) —
 * fired when a user prints/exports the per-trial Report Summary page.
 */
class RecordReportPrint
{
    public function __invoke(Trial $trial, User $user): void
    {
        AuditLog::create([
            'trial_id' => $trial->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => 'report_printed',
            'old_data' => [],
            'new_data' => ['report_type' => 'Report Summary'],
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'action' => 'PRINT_REPORT',
            'module' => 'REPORT',
            'record_id' => (string) $trial->id,
            'record_label' => $trial->trial_code,
            'old_data' => null,
            'new_data' => json_encode(['report_type' => 'Report Summary']),
        ]);
    }
}
