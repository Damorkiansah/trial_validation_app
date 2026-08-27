<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Legacy `trials_weighing` table — per-sample weighing readings for a
 * trial's Weighing (Packaging/Filling) step (public/index.php:561-617).
 * Unlike `trials_results`, this table has a real surrogate `id` primary key
 * (plus a unique(trial_id, section, item_no)), so it's a normal writable
 * Eloquent model — no upsert-via-DB::table workaround needed here.
 *
 * @property int $id
 * @property int $trial_id
 * @property string $section
 * @property int $item_no
 * @property string|null $weight_value
 * @property bool $is_skipped
 * @property Carbon|null $created_at
 */
#[Fillable(['trial_id', 'section', 'item_no', 'weight_value', 'is_skipped'])]
class TrialWeighing extends Model
{
    protected $table = 'trials_weighing';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_skipped' => 'boolean',
            'created_at' => 'datetime',
        ];
    }
}
