<?php

namespace App\Http\Requests\Reviews;

use App\Models\TrialReview;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * Port of the /review/{id}/save validation block in the legacy app's
 * public/index.php:810-823. TrialReviewPolicy::update() covers legacy's
 * department-match check and "is this still the active review" check
 * (die('Forbidden') / die('Review sudah tidak aktif.')) as a single 403,
 * matching legacy's hard-stop behavior for both. The comment-required check
 * stays a normal validation rule.
 */
class SaveDepartmentReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $review = TrialReview::findOrFail($this->route('review'));

        return Gate::allows('update', $review);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'comment' => ['required', 'string'],
        ];
    }
}
