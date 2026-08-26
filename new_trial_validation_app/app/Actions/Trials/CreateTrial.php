<?php

namespace App\Actions\Trials;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Trial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Port of the /trials/store handler (public/index.php:347-403). Header-only
 * (Validation/Weighing/Review/Approval/Attachments are separate Fase 3
 * sub-items) — always lands the trial in Draft/Validation, exactly like
 * legacy.
 */
class CreateTrial
{
    /**
     * @param  array<string, mixed>  $data  Validated StoreTrialRequest data.
     */
    public function __invoke(array $data, User $user): Trial
    {
        return DB::transaction(function () use ($data, $user) {
            $product = Product::where('id', $data['product_id'])->firstOrFail();

            $trial = new Trial([
                ...$data,
                'trial_code' => $this->generateUniqueCode(),
                'product_name' => $product->product_name,
                'finish_good_code' => $product->finish_good_code,
                'current_step' => 'Validation',
                'progress_status' => 'Draft',
                'created_by' => $user->email,
            ]);
            $trial->save();

            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'action' => 'CREATE',
                'module' => 'TRIAL',
                'record_id' => (string) $trial->id,
                'record_label' => $trial->trial_code,
                'old_data' => null,
                'new_data' => json_encode($data),
            ]);

            return $trial;
        });
    }

    /**
     * Legacy generates `TRIAL-{Ymd-His}` with no collision check
     * (public/index.php:396) — a real race condition against the DB's
     * unique key on trial_code if two trials are created in the same
     * second. Adds a short random suffix plus a retry loop instead.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = 'TRIAL-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
        } while (Trial::where('trial_code', $code)->exists());

        return $code;
    }
}
