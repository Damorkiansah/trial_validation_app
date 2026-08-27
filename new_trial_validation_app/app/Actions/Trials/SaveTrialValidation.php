<?php

namespace App\Actions\Trials;

use App\Models\ActivityLog;
use App\Models\Trial;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Port of the /trials/{id}/validation/save save block
 * (public/index.php:549-559). Normalizes each row (N/A forces result='N/A',
 * OK with an empty result forces 'Conform', matching legacy exactly),
 * upserts into trials_results, and unconditionally advances current_step to
 * WeighingPackaging (progress_status is untouched here).
 */
class SaveTrialValidation
{
    /**
     * @param  array<int, array{parameter_id: int|string, decision: string, result: string|null, remark: string|null}>  $results  Validated SaveTrialValidationRequest['results'].
     */
    public function __invoke(Trial $trial, array $results, User $user): Trial
    {
        return DB::transaction(function () use ($trial, $results, $user) {
            $oldRows = DB::table('trials_results')
                ->where('trial_id', $trial->id)
                ->get()
                ->keyBy('parameter_id')
                ->toArray();

            $normalized = [];
            foreach ($results as $row) {
                $decision = $row['decision'];
                $result = trim((string) ($row['result'] ?? ''));
                $remark = trim((string) ($row['remark'] ?? ''));

                if ($decision === 'N/A') {
                    $result = 'N/A';
                } elseif ($decision === 'OK' && $result === '') {
                    $result = 'Conform';
                }

                $normalized[] = [
                    'trial_id' => $trial->id,
                    'parameter_id' => (int) $row['parameter_id'],
                    'result_value' => $result,
                    'decision' => $decision,
                    'remark' => $remark,
                    'updated_at' => Carbon::now(),
                ];
            }

            DB::table('trials_results')->upsert(
                $normalized,
                ['trial_id', 'parameter_id'],
                ['result_value', 'decision', 'remark', 'updated_at'],
            );

            $trial->current_step = 'WeighingPackaging';
            $trial->save();

            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'action' => 'UPDATE',
                'module' => 'VALIDATION',
                'record_id' => (string) $trial->id,
                'record_label' => $trial->trial_code,
                'old_data' => json_encode($oldRows),
                'new_data' => json_encode($normalized),
            ]);

            return $trial;
        });
    }
}
