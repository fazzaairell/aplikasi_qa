<?php

namespace App\Services;

use App\Events\BugStatusChanged;
use App\Models\Bug;
use App\Models\BugNotification;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BugStatusService
{
    public function __construct(
        protected FileUploadService $fileUploadService,
    ) {
    }

    public function updateStatus(
        Bug $bug,
        User $user,
        string $newStatus,
        ?UploadedFile $fixAttachment,
        ?string $manualStartDate = null,
        ?string $manualFinishDate = null,
    ): BugStatusUpdateResult {
        $oldStatus = $bug->status;

        if ($oldStatus === $newStatus) {
            return BugStatusUpdateResult::ok($bug, 'Status bug tidak berubah.', 200);
        }

        $isDeveloper = $user->isDeveloper();
        $isQa = $user->isQa();
        $isAdmin = $user->isAdmin();

        if (! $isDeveloper && ! $isQa && ! $isAdmin) {
            return BugStatusUpdateResult::fail($bug, 'Role kamu tidak memiliki akses untuk mengubah status bug.', 403);
        }

        if ($isDeveloper && (int) $bug->assigned_to !== (int) $user->id) {
            return BugStatusUpdateResult::fail($bug, 'Kamu tidak memiliki akses untuk mengubah bug ini.', 403);
        }

        $allowedTransitions = $isDeveloper
            ? [
                Bug::STATUS_OPEN => [Bug::STATUS_IN_PROGRESS],
                Bug::STATUS_IN_PROGRESS => [Bug::STATUS_DONE_IN_REVIEW],
                Bug::STATUS_REOPENED => [Bug::STATUS_IN_PROGRESS],
            ]
            : [
                Bug::STATUS_DONE_IN_REVIEW => [
                    Bug::STATUS_RESOLVED,
                    Bug::STATUS_REOPENED,
                ],
            ];

        if (! in_array($newStatus, $allowedTransitions[$oldStatus] ?? [], true)) {
            return BugStatusUpdateResult::fail(
                $bug,
                "Perubahan status {$oldStatus} → {$newStatus} tidak diperbolehkan.",
                403
            );
        }

        DB::transaction(function () use ($bug, $oldStatus, $newStatus, $fixAttachment, $user, $manualStartDate, $manualFinishDate) {
            $startDate = $bug->start_date;
            $finishDate = $bug->finish_date;

            if ($newStatus === Bug::STATUS_IN_PROGRESS) {
                $startDate = $manualStartDate ?: ($bug->start_date ?? now());
            } elseif ($newStatus === Bug::STATUS_DONE_IN_REVIEW) {
                $finishDate = $manualFinishDate ?: ($bug->finish_date ?? now());
            } elseif ($newStatus === Bug::STATUS_RESOLVED) {
                $finishDate = $manualFinishDate ?: ($bug->finish_date ?? now());
            } elseif ($newStatus === Bug::STATUS_REOPENED) {
                $startDate = null;
                $finishDate = null;
            }

            $fixAttachmentPath = $bug->fix_attachment;
            if ($fixAttachment) {
                $fixAttachmentPath = $this->fileUploadService->replace(
                    $fixAttachment,
                    'bug-fix-attachments',
                    $bug->fix_attachment
                );
            }

            $bug->update([
                'status' => $newStatus,
                'start_date' => $startDate,
                'finish_date' => $finishDate,
                'fix_attachment' => $fixAttachmentPath,
            ]);

            event(new BugStatusChanged($bug, $oldStatus, $newStatus));

            if ($bug->testResult) {
                if ($newStatus === Bug::STATUS_RESOLVED) {
                    $bug->testResult->update(['status' => TestResult::STATUS_PASSED]);
                } elseif ($newStatus === Bug::STATUS_REOPENED) {
                    $bug->testResult->update(['status' => TestResult::STATUS_FAILED]);
                }
            }

            $this->notify($bug, $newStatus, $user);
        });

        return BugStatusUpdateResult::ok(
            $bug->fresh(['testResult', 'assignee', 'reporter']),
            "Status bug berhasil diubah dari {$oldStatus} menjadi {$newStatus}.",
            200
        );
    }

    protected function notify(Bug $bug, string $newStatus, User $user): void
    {
        if ($newStatus === Bug::STATUS_IN_PROGRESS && $bug->reported_by) {
            BugNotification::create([
                'user_id' => $bug->reported_by,
                'causer_id' => $user->id,
                'bug_id' => $bug->id,
                'type' => 'bug_in_progress',
                'message' => "⚙️ Bug \"{$bug->title}\" sedang dalam pengerjaan oleh Developer ({$user->name}).",
                'is_read' => false,
            ]);
        }

        if ($newStatus === Bug::STATUS_DONE_IN_REVIEW) {
            if ($bug->reported_by) {
                BugNotification::create([
                    'user_id' => $bug->reported_by,
                    'causer_id' => $user->id,
                    'bug_id' => $bug->id,
                    'type' => 'bug_done_review',
                    'message' => "🔔 Bug \"{$bug->title}\" sudah selesai diperbaiki oleh Developer ({$user->name}). Silakan lakukan retest.",
                    'is_read' => false,
                ]);
            }

            $admins = User::where('role', 'Admin')->get();
            foreach ($admins as $admin) {
                BugNotification::create([
                    'user_id' => $admin->id,
                    'causer_id' => $user->id,
                    'bug_id' => $bug->id,
                    'type' => 'bug_done_review',
                    'message' => "📋 Developer {$user->name} menandai bug \"{$bug->title}\" sebagai Done in Review.",
                    'is_read' => false,
                ]);
            }
        }

        if ($newStatus === Bug::STATUS_RESOLVED && $bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'causer_id' => $user->id,
                'bug_id' => $bug->id,
                'type' => 'bug_resolved',
                'message' => "✅ Bug \"{$bug->title}\" berhasil melewati retest dan dinyatakan Resolved oleh {$user->name}.",
                'is_read' => false,
            ]);
        }

        if ($newStatus === Bug::STATUS_REOPENED && $bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'causer_id' => $user->id,
                'bug_id' => $bug->id,
                'type' => 'bug_reopened',
                'message' => "⚠️ Bug \"{$bug->title}\" gagal dalam retest dan di-Reopened oleh QA ({$user->name}). Perlu diperbaiki kembali.",
                'is_read' => false,
            ]);
        }
    }
}