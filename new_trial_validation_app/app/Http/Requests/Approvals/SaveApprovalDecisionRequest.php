<?php

namespace App\Http\Requests\Approvals;

use App\Models\Trial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Port of the validation block in legacy's POST /trials/{id}/approval
 * (public/index.php:884-913) — trial must be Ready for Approval, decision
 * must be one of the 3 known values, comment is required, and the acting
 * user's own account password must be re-entered as an e-signature and
 * verified against their real password_hash.
 */
class SaveApprovalDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $trial = Trial::whereNull('deleted_at')->where('id', $this->route('trial'))->firstOrFail();

        return Gate::allows('approve', $trial);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['Approved', 'Need Revision', 'Rejected'])],
            'approval_comment' => ['required', 'string'],
            'signature_password' => ['required', 'string'],
        ];
    }

    /**
     * @return 'Approved'|'Need Revision'|'Rejected'
     */
    public function decision(): string
    {
        return match ($this->string('decision')->toString()) {
            'Approved' => 'Approved',
            'Need Revision' => 'Need Revision',
            'Rejected' => 'Rejected',
            default => throw new \RuntimeException('Unreachable: decision already validated by rules().'),
        };
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $trial = Trial::whereNull('deleted_at')->where('id', $this->route('trial'))->firstOrFail();

            if ($trial->progress_status !== 'Ready for Approval') {
                $validator->errors()->add('decision', 'Trial belum siap approval.');

                return;
            }

            $password = (string) $this->input('signature_password');
            if ($password === '') {
                return;
            }

            $user = $this->user();
            if (! $user->is_active || ! Hash::check($password, $user->password_hash)) {
                $validator->errors()->add('signature_password', 'Password e-signature salah.');
            }
        });
    }
}
