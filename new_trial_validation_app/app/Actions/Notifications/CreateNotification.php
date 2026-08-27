<?php

namespace App\Actions\Notifications;

use App\Models\Notification;
use App\Models\User;
use Throwable;

/**
 * Port of createNotification()/notification_target_users() in the legacy
 * app's app/bootstrap.php:462-504. Fans a role/department-targeted
 * notification out to every matching active user (one row per user, each
 * carrying the resolved user_id plus the original role_target/
 * department_target for reference) — a literal `user_id` in $data targets
 * exactly that one user instead. Falls back to a single untargeted row
 * (user_id=null) when nothing resolves, matching legacy. Never throws:
 * notifications must never block the calling workflow action, exactly like
 * legacy's own try/catch wrapper.
 */
class CreateNotification
{
    /**
     * @param  array{user_id?: int|null, role_target?: string|null, department_target?: string|null, trial_id?: int|null, title: string, message: string, type?: string}  $data
     */
    public function __invoke(array $data): void
    {
        try {
            $userId = $data['user_id'] ?? null;
            $roleTarget = trim((string) ($data['role_target'] ?? ''));
            $departmentTarget = User::normalizeDepartment($data['department_target'] ?? '');
            $trialId = $data['trial_id'] ?? null;
            $title = trim($data['title']);
            $message = trim($data['message']);
            $type = trim((string) ($data['type'] ?? 'info')) ?: 'info';

            if ($title === '' || $message === '') {
                return;
            }

            $targets = $this->targetUserIds($roleTarget ?: null, $departmentTarget ?: null, $userId ?: null);
            if (! $targets) {
                $targets = [null];
            }

            foreach ($targets as $targetId) {
                $exists = Notification::query()
                    ->where(fn ($q) => $targetId === null ? $q->whereNull('user_id') : $q->where('user_id', $targetId))
                    ->where(fn ($q) => $roleTarget !== '' ? $q->where('role_target', $roleTarget) : $q->whereNull('role_target'))
                    ->where(fn ($q) => $departmentTarget !== '' ? $q->where('department_target', $departmentTarget) : $q->whereNull('department_target'))
                    ->where(fn ($q) => $trialId !== null ? $q->where('trial_id', $trialId) : $q->whereNull('trial_id'))
                    ->where('title', $title)
                    ->where('type', $type)
                    ->where('is_read', false)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Notification::create([
                    'user_id' => $targetId,
                    'role_target' => $roleTarget ?: null,
                    'department_target' => $departmentTarget ?: null,
                    'trial_id' => $trialId,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => false,
                ]);
            }
        } catch (Throwable) {
            // Notifications must never block the main workflow.
        }
    }

    /**
     * @return list<int>
     */
    private function targetUserIds(?string $roleTarget, ?string $departmentTarget, ?int $userId): array
    {
        if ($userId) {
            return array_values(User::query()->where('id', $userId)->where('is_active', 1)->pluck('id')->map(fn ($id) => (int) $id)->all());
        }

        $roleTarget = trim((string) $roleTarget);
        $departmentTarget = User::normalizeDepartment($departmentTarget);

        if ($roleTarget === 'Reviewer' && $departmentTarget !== '') {
            return array_values(User::query()
                ->where('is_active', 1)
                ->where(function ($q) use ($departmentTarget) {
                    $q->whereRaw('UPPER(TRIM(role)) = ?', [$departmentTarget])
                        ->orWhereRaw('UPPER(TRIM(department)) = ?', [$departmentTarget]);
                })
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all());
        }

        if ($roleTarget !== '') {
            return array_values(User::query()
                ->where('is_active', 1)
                ->where('role', $roleTarget)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all());
        }

        return [];
    }
}
