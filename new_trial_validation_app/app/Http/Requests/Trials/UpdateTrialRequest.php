<?php

namespace App\Http\Requests\Trials;

use App\Models\Trial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTrialRequest extends FormRequest
{
    public function authorize(): bool
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($this->route('trial'));

        return Gate::allows('update', $trial);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return TrialRequestRules::rules();
    }
}
