<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\BugNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugController extends Controller
{
    /**
     * Menampilkan daftar bug di halaman Web (Blade).
     */
    public function index()
    {
        $bugs = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'reporter',
            'testResult.testRun',
        ])->latest()->get();

        return view('bugs.index', compact('bugs'));
    }

    /**
     * API: Mengambil semua daftar bug (format JSON).
     */
    public function apiIndex()
    {
        $bugs = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'testResult.testRun',
        ])->get();

        return response()->json(['data' => $bugs]);
    }

    /**
     * Menampilkan detail satu bug.
     */
    public function show(int $id)
    {
        $bug = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'testResult.testRun',
        ])->findOrFail($id);

        return view('bugs.show', compact('bug'));
    }

    /**
     * Memperbarui status Bug.
     *
     * Alur status:
     *   Open → In Progress → Done in Review  (Developer selesai)
     *   QA retest → Closed (lulus) atau Reopened (gagal lagi)
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Open,In Progress,Done in Review,Resolved,Closed,Reopened',
        ]);

        $bug = Bug::findOrFail($id);
        $oldStatus = $bug->status;
        $newStatus = $request->status;

        // ── Tentukan finish_date otomatis ──────────────────────────────────
        if ($newStatus === 'Reopened') {
            $finishDate = null;   // reset finish date saat di-reopen
        } elseif (in_array($newStatus, ['Resolved', 'Closed', 'Done in Review'])) {
            $finishDate = $bug->finish_date
                ? $bug->finish_date->toDateString()
                : now()->toDateString();
        } else {
            $finishDate = $bug->finish_date?->toDateString();
        }

        $bug->update([
            'status'      => $newStatus,
            'finish_date' => $finishDate,
        ]);

        // ── Sinkronisasi TestResult ──────────────────────────────────────
        // Jika Closed → test result otomatis Passed
        if ($newStatus === 'Closed' && $bug->testResult) {
            $bug->testResult->update(['status' => 'Passed']);
        }
        // Jika Reopened → test result kembali Failed
        if ($newStatus === 'Reopened' && $bug->testResult) {
            $bug->testResult->update(['status' => 'Failed']);
        }

        // ── Kirim notifikasi ─────────────────────────────────────────────

        // 1. Developer menandai "Done in Review" → beri tahu QA yang melaporkan
        if ($newStatus === 'Done in Review' && $bug->reported_by) {
            BugNotification::create([
                'user_id' => $bug->reported_by,
                'bug_id'  => $bug->id,
                'type'    => 'bug_done_review',
                'message' => '🔔 Bug "' . $bug->title . '" sudah selesai diperbaiki oleh Developer. Silakan lakukan retest.',
                'is_read' => false,
            ]);
        }

        // 2. QA me-reopen bug → beri tahu Developer yang di-assign
        if ($newStatus === 'Reopened' && $bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'bug_id'  => $bug->id,
                'type'    => 'bug_reopened',
                'message' => '⚠️ Bug "' . $bug->title . '" di-reopen oleh QA setelah retest gagal. Perlu diperbaiki ulang.',
                'is_read' => false,
            ]);
        }

        if (!$request->expectsJson()) {
            return back()->with('success', 'Status bug berhasil diperbarui!');
        }

        return response()->json([
            'message' => 'Status bug berhasil diperbarui!',
            'data'    => $bug,
        ]);
    }
}