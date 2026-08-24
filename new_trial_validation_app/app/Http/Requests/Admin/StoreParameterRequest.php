<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-parameters');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'min:1'],
            'product_type' => ['required', 'string', 'max:100'],
            'parameter_name' => ['required', 'string', 'max:200'],
            'specification' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
