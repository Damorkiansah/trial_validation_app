<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Legacy `trials_results` table — per-parameter validation decision/result/
 * remark rows for a trial's Validation step (public/index.php:493-559).
 * Composite primary key (trial_id, parameter_id), no surrogate id column —
 * confirmed against the real shared MySQL schema. Adding a surrogate id
 * would require an ALTER TABLE on live shared data, which this migration
 * deliberately avoids (see the 2026_08_27_000001 migration).
 *
 * Read-only via Eloquent — do NOT call save()/create()/find() on this model.
 * All writes go through DB::table('trials_results')->upsert(), which builds
 * the same INSERT ... ON DUPLICATE KEY UPDATE legacy does. See
 * App\Actions\Trials\SaveTrialValidation.
 *
 * @property int $trial_id
 * @property int $parameter_id
 * @property string|null $result_value
 * @property string|null $decision
 * @property string|null $remark
 * @property Carbon|null $updated_at
 */
class TrialResult extends Model
{
    protected $table = 'trials_results';

    public $incrementing = false;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }
}
