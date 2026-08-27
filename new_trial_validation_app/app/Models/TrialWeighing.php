<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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

    /**
     * Port of weighing_stats() (app/bootstrap.php:385-403) — computed
     * server-side so the same numbers work for both the Report Summary page
     * and the later Browsershot PDF pass without needing JS.
     *
     * @param  Collection<int, TrialWeighing>  $items
     * @return array{values: list<string>, count: int, min: float|null, max: float|null, avg: float|null}
     */
    public static function statsForSection(Collection $items): array
    {
        $nums = [];
        $values = [];

        foreach ($items as $item) {
            if ($item->is_skipped) {
                continue;
            }

            $value = $item->weight_value;
            if ($value !== null && $value !== '' && is_numeric($value)) {
                $nums[] = (float) $value;
                $values[] = $value;
            }
        }

        return [
            'values' => $values,
            'count' => count($nums),
            'min' => $nums ? min($nums) : null,
            'max' => $nums ? max($nums) : null,
            'avg' => $nums ? array_sum($nums) / count($nums) : null,
        ];
    }
}
