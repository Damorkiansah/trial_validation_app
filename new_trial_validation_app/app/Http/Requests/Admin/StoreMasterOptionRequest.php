<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\MasterOptionController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMasterOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-master');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $types = MasterOptionController::BASE_TYPES;
        if ($this->user()?->isSuperAdmin()) {
            $types = array_merge($types, MasterOptionController::PRIVILEGED_TYPES);
        }

        return [
            'id' => ['nullable', 'integer', 'min:1'],
            'type' => ['required', 'string', Rule::in($types)],
            'name' => ['required', 'string', 'max:200'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = $this->string('type')->toString();
            $name = $this->string('name')->toString();

            if (in_array($type, MasterOptionController::PRIVILEGED_TYPES, true) && strlen($name) > 50) {
                $validator->errors()->add('name', 'Nama role/reviewer maksimal 50 karakter.');
            }
        });
    }
}
