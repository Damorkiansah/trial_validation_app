<?php

namespace App\Http\Requests\Trials;

use App\Models\Trial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Port of the two upfront-blocking checks in the /trials/{id}/attachments/
 * upload block (public/index.php:633-652) — an invalid category, or no files
 * submitted at all. Per-file mime/size/move failures are NOT validated here;
 * those are handled with partial-success semantics by
 * App\Actions\Trials\SaveTrialAttachments, matching legacy exactly (it saves
 * whatever files are valid and reports the rest as errors, rather than
 * rejecting the whole upload).
 */
class StoreTrialAttachmentsRequest extends FormRequest
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
            'category' => [
                'required',
                'string',
                Rule::exists('master_options', 'name')
                    ->where('type', 'attachment_category')
                    ->where('is_active', 1),
            ],
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['nullable', 'file'],
        ];
    }
}
