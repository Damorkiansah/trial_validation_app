<?php

namespace App\Actions\Trials;

use App\Models\ActivityLog;
use App\Models\Trial;
use App\Models\TrialAttachmentFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Port of the /trials/{id}/attachments/upload save block
 * (public/index.php:630-686). Unlike Validation/Weighing, this is NOT
 * all-or-nothing: legacy processes every submitted file independently
 * (mime/size checks per file), saving what's valid and collecting an error
 * message per invalid one, so this action reproduces that same partial-
 * success shape rather than rejecting the whole batch on the first bad file.
 * `category`/"at least one file submitted" are the only two upfront-blocking
 * checks, both handled by StoreTrialAttachmentsRequest before this runs.
 */
class SaveTrialAttachments
{
    /**
     * @var array<string, string>
     */
    private const ALLOWED_MIME_TO_EXTENSION = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    private const MAX_SIZE_BYTES = 10 * 1024 * 1024;

    /**
     * @param  array<int, UploadedFile|null>  $photos
     * @return array{saved: int, errors: array<int, string>}
     */
    public function __invoke(Trial $trial, string $category, array $photos, User $user): array
    {
        $disk = Storage::disk('legacy_uploads');
        $saved = 0;
        $errors = [];

        foreach ($photos as $i => $photo) {
            if (! $photo instanceof UploadedFile || ! $photo->isValid()) {
                $errors[] = 'Upload file ke-'.($i + 1).' gagal.';

                continue;
            }

            $originalName = $photo->getClientOriginalName();

            if ($photo->getSize() > self::MAX_SIZE_BYTES) {
                $errors[] = "File {$originalName} melebihi 10 MB.";

                continue;
            }

            $extension = self::ALLOWED_MIME_TO_EXTENSION[$photo->getMimeType()] ?? null;

            if ($extension === null) {
                $errors[] = "File {$originalName} bukan gambar yang diizinkan.";

                continue;
            }

            $name = bin2hex(random_bytes(16)).'.'.$extension;

            if ($disk->putFileAs((string) $trial->id, $photo, $name) === false) {
                $errors[] = "File {$originalName} gagal disimpan.";

                continue;
            }

            TrialAttachmentFile::create([
                'trial_id' => $trial->id,
                'category' => $category,
                'file_name' => $name,
                'file_path' => "/uploads/{$trial->id}/{$name}",
                'uploaded_by' => $user->email,
            ]);

            $saved++;
        }

        $trial->current_step = 'Attachment';
        $trial->save();

        if ($saved > 0) {
            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'action' => 'CREATE',
                'module' => 'ATTACHMENT',
                'record_id' => (string) $trial->id,
                'record_label' => $trial->trial_code,
                'old_data' => null,
                'new_data' => json_encode(['category' => $category, 'count' => $saved]),
            ]);
        }

        return ['saved' => $saved, 'errors' => $errors];
    }
}
