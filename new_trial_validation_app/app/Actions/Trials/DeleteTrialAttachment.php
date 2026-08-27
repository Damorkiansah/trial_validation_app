<?php

namespace App\Actions\Trials;

use App\Models\ActivityLog;
use App\Models\TrialAttachmentFile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Port of the /trials/{id}/attachments/{id}/delete block
 * (public/index.php:688-710) — a genuine hard delete of both the DB row and
 * the physical file (legacy guards the unlink with a realpath/str_starts_with
 * containment check; here that's implicit since the relative path is built
 * from the row's own trial_id/file_name, never from user input).
 */
class DeleteTrialAttachment
{
    public function __invoke(TrialAttachmentFile $attachment, User $user): void
    {
        $snapshot = $attachment->only(['id', 'trial_id', 'category', 'file_name', 'file_path', 'uploaded_by']);
        $relativePath = $attachment->trial_id.'/'.$attachment->file_name;

        $attachment->delete();

        Storage::disk('legacy_uploads')->delete($relativePath);

        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'action' => 'DELETE',
            'module' => 'ATTACHMENT',
            'record_id' => (string) $snapshot['id'],
            'record_label' => $snapshot['file_name'],
            'old_data' => json_encode($snapshot),
            'new_data' => null,
        ]);
    }
}
