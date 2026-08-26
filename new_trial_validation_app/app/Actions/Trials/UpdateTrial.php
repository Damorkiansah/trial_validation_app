<?php

namespace App\Actions\Trials;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Trial;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Port of the /trials/{id}/update handler (public/index.php:413-483).
 * Header-only — never touches progress_status/current_step/trial_code/
 * created_by/revision_no, matching legacy exactly.
 */
class UpdateTrial
{
    /**
     * @param  array<string, mixed>  $data  Validated UpdateTrialRequest data.
     */
    public function __invoke(Trial $trial, array $data, User $user): Trial
    {
        return DB::transaction(function () use ($trial, $data, $user) {
            $oldData = $trial->only(array_keys($data));

            $product = Product::where('id', $data['product_id'])->firstOrFail();

            $trial->fill([
                ...$data,
                'product_name' => $product->product_name,
                'finish_good_code' => $product->finish_good_code,
            ]);
            $trial->save();

            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'action' => 'UPDATE',
                'module' => 'TRIAL',
                'record_id' => (string) $trial->id,
                'record_label' => $trial->trial_code,
                'old_data' => json_encode($oldData),
                'new_data' => json_encode($data),
            ]);

            return $trial;
        });
    }
}
