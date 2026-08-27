<?php

namespace App\Http\Requests\Trials;

use App\Actions\Trials\CheckTrialCompleteness;
use App\Models\Trial;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Port of the /trials/{id}/submit-review validation block in the legacy
 * app's public/index.php:740-762 — wizard Step 6 (Review & Submit). Legacy
 * checks completeness (trial_completeness()) before even showing the
 * Submit-for-Review button; here it's re-checked server-side as a
 * cross-field rule so a stale page can't bypass it.
 */
class SubmitTrialForReviewRequest extends FormRequest
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
            'departments' => ['required', 'array', 'min:1'],
            'departments.*' => [Rule::in(User::reviewerDepartmentCodes())],
            'approver_user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('is_active', 1)->whereNull('deleted_at'),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $trial = Trial::whereNull('deleted_at')->where('id', $this->route('trial'))->firstOrFail();
            $errors = (new CheckTrialCompleteness)($trial);

            if ($errors) {
                $validator->errors()->add('completeness', 'Belum bisa submit review: '.implode(' | ', $errors));
            }
        });
    }

    /**
     * @return list<string>
     */
    public function departments(): array
    {
        return array_values(array_unique((array) $this->input('departments', [])));
    }
}
