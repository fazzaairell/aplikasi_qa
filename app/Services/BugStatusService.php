<?php

namespace App\Services;

use App\Events\BugStatusChanged;
use App\Models\Bug;
use App\Models\BugNotification;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Mengatur alur perubahan status Bug sesuai workflow QA:
 *
 *   Developer      : Open/Reopened -> In Progress -> Done in Review
 *   QA/Admin       : Done in Review -> Resolved atau Reopened
 *
 * Termasuk: cek otorisasi & alur transisi, upload bukti perbaikan developer,
 * sinkronisasi TestResult, pencatatan history (lewat event), dan notifikasi.
 * Ditarik keluar dari BugController supaya controller tetap tipis dan logic
 * ini bisa dites terpisah.
 */
class BugStatusService
{
    public function __construct(
        protected FileUploadService $fileUploadService,
    ) {
    }

    public function updateStatus(Bug $bug, User $user, string $newStatus, ?UploadedFile $fixAttachment): BugStatusUpdateResult
    {
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

        // Developer hanya boleh mengubah bug yang memang ditugaskan kepadanya.
        if ($isDeveloper && (int) $bug->assigned_to !== (int) $user->id) {
            return BugStatusUpdateResult::fail($bug, 'Kamu tidak memiliki akses untuk mengubah bug ini.', 403);
        }

        $allowedTransitions = $isDeveloper
            ? [
                Bug::STATUS_OPEN => [Bug::STATUS_IN_PROGRESS],
                Bug::STATUS_IN_PROGRESS => [Bug::STATUS_DONE_IN_REVIEW],
                Bug::STATUS_REOPENED => [Bug::STATUS_IN_PROGRESS],
            ]
            // QA menentukan hasil retest. Admin mengikuti workflow yang sama
            // agar status tidak dapat dilompati sembarangan.
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

        DB::transaction(function () use ($bug, $oldStatus, $newStatus, $fixAttachment, $user) {
            $finishDate = $bug->finish_date;

            if ($newStatus === Bug::STATUS_RESOLVED) {
                $finishDate = $bug->finish_date ?? now();
            } elseif ($newStatus === Bug::STATUS_REOPENED) {
                $finishDate = null;
            }

            // Developer bisa melampirkan file bukti perbaikan (screenshot/foto) saat mengubah status.
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
                'finish_date' => $finishDate,
                'fix_attachment' => $fixAttachmentPath,
            ]);

            // History selalu dicatat untuk perubahan status yang berhasil.
            event(new BugStatusChanged($bug, $oldStatus, $newStatus));

            // Sinkronisasi TestResult hanya dilakukan dari hasil retest QA.
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

    /**
     * Kirim notifikasi ke pihak terkait sesuai status baru.
     */
    protected function notify(Bug $bug, string $newStatus, User $user): void
    {
        // Developer mulai memperbaiki bug.
        if ($newStatus === Bug::STATUS_IN_PROGRESS && $bug->reported_by) {
            BugNotification::create([
                'user_id' => $bug->reported_by,
                'bug_id' => $bug->id,
                'type' => 'bug_in_progress',
                'message' => "⚙️ Bug \"{$bug->title}\" sedang dalam pengerjaan oleh Developer ({$user->name}).",
                'is_read' => false,
            ]);
        }

        // Developer selesai memperbaiki bug.
        if ($newStatus === Bug::STATUS_DONE_IN_REVIEW) {
            if ($bug->reported_by) {
                BugNotification::create([
                    'user_id' => $bug->reported_by,
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
                    'bug_id' => $bug->id,
                    'type' => 'bug_done_review',
                    'message' => "📋 Developer {$user->name} menandai bug \"{$bug->title}\" sebagai Done in Review.",
                    'is_read' => false,
                ]);
            }
        }

        // QA berhasil melakukan retest.
        if ($newStatus === Bug::STATUS_RESOLVED && $bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'bug_id' => $bug->id,
                'type' => 'bug_resolved',
                'message' => "✅ Bug \"{$bug->title}\" berhasil melewati retest dan dinyatakan Resolved oleh {$user->name}.",
                'is_read' => false,
            ]);
        }

        // QA gagal melakukan retest.
        if ($newStatus === Bug::STATUS_REOPENED && $bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'bug_id' => $bug->id,
                'type' => 'bug_reopened',
                'message' => "⚠️ Bug \"{$bug->title}\" gagal dalam retest dan di-Reopened oleh QA ({$user->name}). Perlu diperbaiki kembali.",
                'is_read' => false,
            ]);
        }
    }
}
