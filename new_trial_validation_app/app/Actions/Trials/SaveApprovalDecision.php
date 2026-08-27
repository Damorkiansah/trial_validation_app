<?php

namespace App\Actions\Trials;

use App\Actions\Notifications\CreateNotification;
use App\Models\ActivityLog;
use App\Models\Trial;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Port of the POST /trials/{id}/approval decision block in the legacy app's
 * public/index.php:899-981 — Manager QAC (or an assigned Team
 * Leader/Part Leader/Team Leader QA/other approver) makes the final call on
 * a Ready-for-Approval trial. SaveApprovalDecisionRequest already validated
 * the trial's status, the decision value, the comment, and the e-signature
 * password before this runs; this action just performs the state transition
 * and notifies.
 */
class SaveApprovalDecision
{
    /**
     * @param  'Approved'|'Need Revision'|'Rejected'  $decision
     */
    public function __invoke(Trial $trial, string $decision, string $comment, User $approver): Trial
    {
        $approverName = $approver->name ?: $approver->email;

        DB::transaction(function () use ($trial, $decision, $comment, $approverName, $approver) {
            match ($decision) {
                'Approved' => $trial->fill([
                    'progress_status' => 'Approved',
                    'final_decision' => 'Approved',
                    'pending_with' => '',
                    'approved_by' => $approverName,
                    'approved_at' => Carbon::now(),
                    'approval_comment' => $comment,
                ]),
                'Need Revision' => $trial->fill([
                    'progress_status' => 'Need Revision',
                    'current_step' => 'Revision',
                    'final_decision' => 'Need Revision',
                    'pending_with' => 'Staff',
                    'revision_no' => $trial->revision_no + 1,
                    'rejected_by' => $approverName,
                    'rejected_at' => Carbon::now(),
                    'approval_comment' => $comment,
                ]),
                'Rejected' => $trial->fill([
                    'progress_status' => 'Rejected',
                    'current_step' => 'Closed',
                    'final_decision' => 'Rejected',
                    'pending_with' => '',
                    'rejected_by' => $approverName,
                    'rejected_at' => Carbon::now(),
                    'approval_comment' => $comment,
                ]),
            };
            $trial->save();

            ActivityLog::create([
                'user_id' => $approver->id,
                'user_name' => $approver->name,
                'user_role' => $approver->role,
                'action' => match ($decision) {
                    'Approved' => 'APPROVE',
                    'Need Revision' => 'NEED_REVISION',
                    'Rejected' => 'REJECT',
                },
                'module' => 'APPROVAL',
                'record_id' => (string) $trial->id,
                'record_label' => $trial->trial_code,
                'old_data' => null,
                'new_data' => json_encode(['decision' => $decision, 'comment' => $comment, 'manager_name' => $approverName]),
            ]);
        });

        $this->notify($trial, $decision, $approverName);

        return $trial->fresh();
    }

    /**
     * @param  'Approved'|'Need Revision'|'Rejected'  $decision
     */
    private function notify(Trial $trial, string $decision, string $approverName): void
    {
        [$title, $type, $creatorMessage, $adminMessage] = match ($decision) {
            'Approved' => [
                'Trial Approved',
                'approved',
                "Trial {$trial->trial_code} - {$trial->product_name} sudah approved oleh Manager QAC.",
                "Trial {$trial->trial_code} - {$trial->product_name} sudah approved oleh Manager QAC.",
            ],
            'Need Revision' => [
                'Trial Need Revision',
                'revision',
                "Trial {$trial->trial_code} membutuhkan revisi.",
                "Trial {$trial->trial_code} - {$trial->product_name} dikembalikan ke Staff untuk revisi.",
            ],
            'Rejected' => [
                'Trial Rejected',
                'rejected',
                "Trial {$trial->trial_code} - {$trial->product_name} ditolak final oleh Manager QAC.",
                "Trial {$trial->trial_code} - {$trial->product_name} ditolak final oleh Manager QAC.",
            ],
        };

        $creator = User::query()
            ->where('is_active', 1)
            ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim((string) $trial->created_by))])
            ->first();

        if ($creator) {
            (new CreateNotification)([
                'user_id' => $creator->id,
                'role_target' => 'Staff',
                'trial_id' => $trial->id,
                'title' => $title,
                'message' => $creatorMessage,
                'type' => $type,
            ]);
        }

        (new CreateNotification)([
            'role_target' => 'Admin',
            'trial_id' => $trial->id,
            'title' => $title,
            'message' => $adminMessage,
            'type' => $type,
        ]);
    }
}
