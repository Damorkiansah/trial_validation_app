<?php

namespace App\Http\Requests\Trials;

use App\Models\Trial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

/**
 * Port of the /trials/{id}/weighing/{section}/save validation block
 * (public/index.php:583-606). When `skip` is checked, no other validation
 * applies. Otherwise every non-blank `w[item_no]` entry must be numeric and
 * non-negative (and keyed by a plain integer item_no), and at least one
 * entry is required.
 */
class SaveTrialWeighingRequest extends FormRequest
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
            'skip' => ['nullable', 'boolean'],
            'w' => ['nullable', 'array'],
            'w.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty() || $this->boolean('skip')) {
                return;
            }

            $validCount = 0;
            foreach ((array) $this->input('w', []) as $itemNo => $value) {
                $value = trim((string) $value);
                if ($value === '') {
                    continue;
                }

                if (! ctype_digit((string) $itemNo) || ! is_numeric($value) || (float) $value < 0) {
                    $validator->errors()->add('w', 'Weighing sample must be numeric and cannot be negative.');

                    return;
                }

                $validCount++;
            }

            if ($validCount === 0) {
                $validator->errors()->add('w', 'Please input at least 1 weighing sample or click Skip.');
            }
        });
    }
}
