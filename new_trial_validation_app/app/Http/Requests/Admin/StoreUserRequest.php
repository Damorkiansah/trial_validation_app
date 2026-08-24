<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);
        $data['role'] = trim($data['role']);
        $data['department'] = User::normalizeDepartment($data['department'] ?? '');

        return $data;
    }
}
