<?php

namespace App\Http\Requests\Trials;

use Illuminate\Validation\Rule;

/**
 * Shared validation rules for the trial header create/edit form, used by
 * both StoreTrialRequest and UpdateTrialRequest since legacy enforces the
 * same battery of checks in both /trials/store and /trials/{id}/update
 * (public/index.php:351-390 and 415-461). Replaces legacy's manual
 * option_exists()/options_exist() master-option checks with Rule::exists().
 */
class TrialRequestRules
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(): array
    {
        $masterOption = fn (string $type) => Rule::exists('master_options', 'name')
            ->where('type', $type)
            ->where('is_active', 1);

        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('is_active', 1)->whereNull('deleted_at'),
            ],
            'product_type' => ['required', 'string', $masterOption('product_type')],
            'validation_date' => ['required', 'date'],
            'validation_category' => ['required', 'string', $masterOption('validation_category')],
            'risk_level' => ['required', Rule::in(['Low', 'Medium', 'High'])],
            'validation_scope' => ['required', 'array', 'min:1'],
            'validation_scope.*' => ['string', $masterOption('validation_scope')],
            'machine_used' => ['required', 'array', 'min:1'],
            'machine_used.*' => ['string', $masterOption('machine_used')],
            'estimate_qty' => ['required', 'numeric', 'min:0'],
            'batch_number' => ['required', 'string', 'max:200'],
            'bulk_code' => ['required', 'string', 'max:200'],
            'support_team' => ['required', 'string', 'max:200'],
            'initiated_person_team' => ['required', 'string', 'max:200'],
            'reason' => ['required', 'string'],
            'bom' => ['required', 'string'],
        ];
    }
}
