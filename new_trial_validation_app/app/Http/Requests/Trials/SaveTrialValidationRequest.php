<?php

namespace App\Http\Requests\Trials;

use App\Models\Trial;
use App\Models\ValidationParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Port of the /trials/{id}/validation/save validation block
 * (public/index.php:506-548) — an all-or-nothing save of one result row per
 * active validation_parameters row for the trial's product_type.
 */
class SaveTrialValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $trial = Trial::whereNull('deleted_at')->where('id', $this->route('trial'))->firstOrFail();

        return Gate::allows('update', $trial);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'results' => ['required', 'array', 'min:1'],
            'results.*.parameter_id' => ['required', 'integer'],
            'results.*.decision' => ['required', 'string', Rule::in(['OK', 'NOT OK', 'N/A'])],
            'results.*.result' => ['nullable', 'string'],
            'results.*.remark' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $trial = Trial::whereNull('deleted_at')->where('id', $this->route('trial'))->firstOrFail();

            $parameters = ValidationParameter::query()
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->where('product_type', $trial->product_type)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $submitted = collect((array) $this->input('results', []))->keyBy('parameter_id');

            foreach ($parameters as $parameter) {
                $row = $submitted->get($parameter->id);

                if (! $row) {
                    $validator->errors()->add('results', "Parameter {$parameter->parameter_name} belum terisi.");

                    continue;
                }

                $decision = $row['decision'] ?? '';
                $result = trim((string) ($row['result'] ?? ''));
                $remark = trim((string) ($row['remark'] ?? ''));

                if ($decision === 'NOT OK' && ($result === '' || $remark === '')) {
                    $validator->errors()->add('results', "Parameter {$parameter->parameter_name} NOT OK wajib isi Result dan Remark.");
                }
            }
        });
    }
}
