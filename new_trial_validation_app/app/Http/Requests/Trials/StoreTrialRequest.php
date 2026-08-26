<?php

namespace App\Http\Requests\Trials;

use App\Models\Trial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreTrialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Trial::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return TrialRequestRules::rules();
    }
}
