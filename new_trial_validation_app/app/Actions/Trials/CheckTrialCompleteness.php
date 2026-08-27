<?php

namespace App\Actions\Trials;

use App\Models\Trial;
use App\Models\ValidationParameter;
use Illuminate\Support\Facades\DB;

/**
 * Port of trial_completeness() in the legacy app's app/bootstrap.php:735-768
 * — the gate legacy's Report page checks before allowing "Submit for
 * Review". Checks the same header fields legacy does, plus that every
 * active validation_parameters row for the trial's product_type has a
 * decision (and NOT OK rows have a result + remark) — weighing/attachments
 * are deliberately NOT checked here, matching legacy exactly.
 */
class CheckTrialCompleteness
{
    /**
     * @var array<string, string>
     */
    private const HEADER_REQUIRED = [
        'batch_number' => 'Batch Number',
        'bulk_code' => 'Bulk Code',
        'support_team' => 'Support Team',
        'initiated_person_team' => 'Initiated person/team',
        'reason' => 'Reason',
        'bom' => 'B.O.M',
    ];

    /**
     * @return list<string>
     */
    public function __invoke(Trial $trial): array
    {
        $errors = [];

        foreach (self::HEADER_REQUIRED as $field => $label) {
            if (trim((string) $trial->{$field}) === '') {
                $errors[] = "{$label} wajib diisi.";
            }
        }

        $params = ValidationParameter::query()
            ->where('product_type', $trial->product_type)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($params->isEmpty()) {
            $errors[] = "Parameter validation untuk product type {$trial->product_type} belum dikonfigurasi.";

            return $errors;
        }

        $results = DB::table('trials_results')
            ->where('trial_id', $trial->id)
            ->whereIn('parameter_id', $params->pluck('id'))
            ->get()
            ->keyBy('parameter_id');

        foreach ($params as $param) {
            $result = $results->get($param->id);

            if (! $result || ! in_array($result->decision, ['OK', 'NOT OK', 'N/A'], true)) {
                $errors[] = "Parameter {$param->parameter_name} belum memiliki decision.";

                continue;
            }

            if ($result->decision === 'NOT OK' && (trim((string) $result->result_value) === '' || trim((string) $result->remark) === '')) {
                $errors[] = "Parameter {$param->parameter_name} NOT OK wajib punya result dan remark.";
            }
        }

        return $errors;
    }
}
