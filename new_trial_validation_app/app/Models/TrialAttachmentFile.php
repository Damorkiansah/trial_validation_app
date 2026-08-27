<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Legacy `trial_attachment_files` table — uploaded evidence photos for a
 * trial's Attachments step (public/index.php:619-711). Has a real
 * auto-increment `id`, so it's a normal writable Eloquent model, unlike
 * TrialResult. `deleted_at`/`deleted_by` exist in the schema but neither
 * legacy nor this app ever sets them — the delete action
 * (App\Actions\Trials\DeleteTrialAttachment) does a genuine hard delete,
 * matching legacy's `DELETE FROM trial_attachment_files` exactly.
 *
 * Physical file lives on the `legacy_uploads` disk at
 * "{trial_id}/{file_name}" — see config/filesystems.php.
 *
 * @property int $id
 * @property int $trial_id
 * @property string $category
 * @property string $file_name
 * @property string $file_path
 * @property string|null $uploaded_by
 * @property Carbon|null $created_at
 */
#[Fillable(['trial_id', 'category', 'file_name', 'file_path', 'uploaded_by'])]
class TrialAttachmentFile extends Model
{
    protected $table = 'trial_attachment_files';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Trial, $this>
     */
    public function trial(): BelongsTo
    {
        return $this->belongsTo(Trial::class, 'trial_id');
    }
}
