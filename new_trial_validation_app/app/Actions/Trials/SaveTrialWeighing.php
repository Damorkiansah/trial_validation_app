<?php

namespace App\Actions\Trials;

use App\Models\ActivityLog;
use App\Models\Trial;
use App\Models\TrialWeighing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Port of the /trials/{id}/weighing/{section}/save save block
 * (public/index.php:583-616). Legacy always replaces the whole section's
 * rows wholesale (DELETE then INSERT) rather than upserting row-by-row, so
 * this does the same. Skip stores a single sentinel row
 * (item_no=1, weight_value=null, is_skipped=1); otherwise every non-blank
 * `w[item_no]` entry becomes its own row. Advances current_step to
 * WeighingFilling (after Packaging) or Attachment (after Filling) —
 * progress_status is untouched here, matching legacy.
 */
class SaveTrialWeighing
{
    /**
     * @param  array<int|string, string|null>  $rawValues  Raw w[item_no] => value pairs.
     */
    public function __invoke(Trial $trial, string $section, bool $skip, array $rawValues, User $user): Trial
    {
        return DB::transaction(function () use ($trial, $section, $skip, $rawValues, $user) {
            $oldRows = TrialWeighing::query()
                ->where('trial_id', $trial->id)
                ->where('section', $section)
                ->get();

            TrialWeighing::query()
                ->where('trial_id', $trial->id)
                ->where('section', $section)
                ->delete();

            $saved = [];
            if ($skip) {
                $saved[] = TrialWeighing::create([
                    'trial_id' => $trial->id,
                    'section' => $section,
                    'item_no' => 1,
                    'weight_value' => null,
                    'is_skipped' => true,
                ]);
            } else {
                foreach ($rawValues as $itemNo => $value) {
                    $value = trim((string) $value);
                    if ($value === '') {
                        continue;
                    }

                    $saved[] = TrialWeighing::create([
                        'trial_id' => $trial->id,
                        'section' => $section,
                        'item_no' => (int) $itemNo,
                        'weight_value' => $value,
                        'is_skipped' => false,
                    ]);
                }
            }

            $trial->current_step = $section === 'Packaging' ? 'WeighingFilling' : 'Attachment';
            $trial->save();

            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'action' => 'UPDATE',
                'module' => 'WEIGHING',
                'record_id' => (string) $trial->id,
                'record_label' => $trial->trial_code,
                'old_data' => json_encode($oldRows),
                'new_data' => json_encode($saved),
            ]);

            return $trial;
        });
    }
}
