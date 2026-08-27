<?php

namespace App\Http\Controllers;

use App\Actions\Trials\DeleteTrialAttachment;
use App\Actions\Trials\SaveTrialAttachments;
use App\Http\Requests\Trials\StoreTrialAttachmentsRequest;
use App\Models\MasterOption;
use App\Models\Trial;
use App\Models\TrialAttachmentFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Port of the /trials/{id}/attachments (GET), /trials/{id}/attachments/upload
 * (POST), and /trials/{id}/attachments/{id}/delete (POST) handlers in the
 * legacy app's public/index.php:619-711 — wizard Step 5. Separate controller
 * from TrialController/TrialValidationController/TrialWeighingController,
 * matching this app's one-controller-per-feature-slice convention.
 *
 * Unlike legacy (which serves uploaded photos as plain static files under
 * public/uploads/, no auth check at all), `show()` streams the file through
 * an authenticated+authorized route — see config/filesystems.php's
 * `legacy_uploads` disk doc comment for why the physical file still lives in
 * the legacy app's public/uploads/ directory.
 */
class TrialAttachmentController extends Controller
{
    public function edit(int $trial): Response
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        Gate::authorize('view', $trial);

        $categories = MasterOption::query()
            ->where('type', 'attachment_category')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        $files = $trial->attachments()
            ->whereNull('deleted_at')
            ->orderBy('category')
            ->orderBy('id')
            ->get();

        return Inertia::render('trials/attachments', [
            'trial' => $trial,
            'categories' => $categories,
            'files' => $files->map(fn (TrialAttachmentFile $file) => [
                'id' => $file->id,
                'category' => $file->category,
                'file_name' => $file->file_name,
                'url' => route('trials.attachments.show', [$trial->id, $file->id]),
            ]),
            'canEdit' => Gate::allows('update', $trial),
        ]);
    }

    public function store(StoreTrialAttachmentsRequest $request, int $trial, SaveTrialAttachments $action): RedirectResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        $result = $action(
            $trial,
            (string) $request->string('category'),
            (array) $request->file('photos', []),
            $request->user(),
        );

        if ($result['saved'] === 0 && $result['errors']) {
            Inertia::flash('toast', ['type' => 'error', 'message' => implode(' | ', $result['errors'])]);
        } elseif ($result['errors']) {
            Inertia::flash('toast', ['type' => 'warning', 'message' => 'Sebagian file gagal: '.implode(' | ', $result['errors'])]);
        } elseif ($result['saved'] > 0) {
            Inertia::flash('toast', ['type' => 'success', 'message' => "{$result['saved']} foto berhasil diupload."]);
        }

        return to_route('trials.attachments.edit', $trial);
    }

    public function destroy(int $trial, int $attachment, DeleteTrialAttachment $action): RedirectResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        Gate::authorize('update', $trial);

        $file = TrialAttachmentFile::where('trial_id', $trial->id)->findOrFail($attachment);

        $action($file, request()->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Attachment berhasil dihapus.']);

        return to_route('trials.attachments.edit', $trial);
    }

    public function show(int $trial, int $attachment): StreamedResponse
    {
        $trial = Trial::whereNull('deleted_at')->findOrFail($trial);

        Gate::authorize('view', $trial);

        $file = TrialAttachmentFile::where('trial_id', $trial->id)->findOrFail($attachment);

        return Storage::disk('legacy_uploads')->response(
            $trial->id.'/'.$file->file_name,
            $file->file_name,
        );
    }
}
