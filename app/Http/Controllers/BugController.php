<?php

namespace App\Http\Controllers;

use App\Events\BugStatusChanged;
use App\Models\Bug;
use App\Models\BugNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BugController extends Controller
{
    /**
     * Daftar bug aktif (project status != Selesai)
     */
    public function index(Request $request)
    {
        $query = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'reporter',
            'testResult.testRun',
        ])
        ->whereHas('testResult.testCase.testSuite.project', function ($q) {
            $q->where('status', '!=', 'Selesai');
        });

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }
        if ($request->filled('project_id')) {
            $query->whereHas('testResult.testCase.testSuite.project', fn($q) =>
                $q->where('id', $request->project_id)
            );
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bugs     = $query->latest()->get();
        $projects = \App\Models\Project::where('status', '!=', 'Selesai')->get();
        $developers = \App\Models\User::where('role', 'Developer')->get();
        $isHistory = false;

        return view('bugs.index', compact('bugs', 'isHistory', 'projects', 'developers'));
    }

    /**
     * Riwayat bug dari project yang sudah Selesai
     */
    public function history(Request $request)
    {
        $query = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'reporter',
            'testResult.testRun',
        ])
        ->whereHas('testResult.testCase.testSuite.project', fn($q) =>
            $q->where('status', '=', 'Selesai')
        );

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }
        if ($request->filled('project_id')) {
            $query->whereHas('testResult.testCase.testSuite.project', fn($q) =>
                $q->where('id', $request->project_id)
            );
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bugs      = $query->latest()->get();
        $projects  = \App\Models\Project::where('status', '=', 'Selesai')->get();
        $developers = \App\Models\User::where('role', 'Developer')->get();
        $isHistory = true;

        return view('bugs.index', compact('bugs', 'isHistory', 'projects', 'developers'));
    }

    /**
     * Buat bug baru secara standalone (tanpa test result)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'expected_result' => 'nullable|string',
            'assigned_to'     => 'required|exists:users,id',
            'due_date'        => 'required|date',
            'attachment'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file      = $request->file('attachment');
            $filename  = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $directory = public_path('uploads/bug-attachments');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $file->move($directory, $filename);
            $attachmentPath = 'bug-attachments/' . $filename;
        }

        $bug = Bug::create([
            'test_result_id'  => null,
            'title'           => $request->title,
            'description'     => $request->description,
            'expected_result' => $request->expected_result,
            'status'          => 'Open',
            'assigned_to'     => $request->assigned_to,
            'reported_by'     => Auth::id(),
            'due_date'        => $request->due_date,
            'attachment'      => $attachmentPath,
        ]);

        // Notifikasi ke Developer yang di-assign
        $reporter = Auth::user();
        if ($bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'bug_id'  => $bug->id,
                'type'    => 'bug_reported',
                'message' => "🐛 Bug baru dilaporkan oleh {$reporter->name}: \"{$bug->title}\". Segera ditangani!",
                'is_read' => false,
            ]);
        }

        // Notifikasi ke semua Admin
        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            BugNotification::create([
                'user_id' => $admin->id,
                'bug_id'  => $bug->id,
                'type'    => 'bug_reported',
                'message' => "📋 Bug baru: \"{$bug->title}\" dilaporkan oleh {$reporter->name}.",
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Bug berhasil dilaporkan!');
    }

    /**
     * Detail satu bug
     */
    public function show(int $id)
    {
        $bug = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'reporter',
            'testResult.testRun',
        ])->findOrFail($id);

        return view('bugs.show', compact('bug'));
    }

    /**
     * Update status bug
     *
     * Developer  → hanya boleh: In Progress, Done in Review
     * Admin / QA → bisa semua status
     */
    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();
        $bug  = Bug::with(['testResult', 'assignee', 'reporter'])->findOrFail($id);

        if ($user->role === 'Developer') {
            $request->validate([
                'status' => 'required|in:In Progress,Done in Review',
            ]);
        } else {
            $request->validate([
                'status' => 'required|in:Open,In Progress,Done in Review,Resolved,Closed,Reopened',
            ]);
        }

        $oldStatus = $bug->status;
        $newStatus = $request->status;

        // Tentukan finish_date otomatis
        if ($newStatus === 'Reopened') {
            $finishDate = null;
        } elseif (in_array($newStatus, ['Resolved', 'Closed', 'Done in Review'])) {
            $finishDate = $bug->finish_date?->toDateString() ?? now()->toDateString();
        } else {
            $finishDate = $bug->finish_date?->toDateString();
        }

        $bug->update([
            'status'      => $newStatus,
            'finish_date' => $finishDate,
        ]);

        // Fire event untuk history tracking
        if (class_exists(\App\Events\BugStatusChanged::class)) {
            try { event(new BugStatusChanged($bug, $oldStatus, $newStatus)); } catch (\Throwable $e) {}
        }

        // ── Sinkronisasi TestResult ───────────────────────────────────────
        if ($newStatus === 'Closed' && $bug->testResult) {
            $bug->testResult->update(['status' => 'Passed']);
        }
        if ($newStatus === 'Reopened' && $bug->testResult) {
            $bug->testResult->update(['status' => 'Failed']);
        }

        // ── Notifikasi ────────────────────────────────────────────────────

        // 1. Developer selesai perbaiki → beri tahu QA yang melaporkan
        if ($newStatus === 'Done in Review' && $bug->reported_by) {
            BugNotification::create([
                'user_id' => $bug->reported_by,
                'bug_id'  => $bug->id,
                'type'    => 'bug_done_review',
                'message' => "🔔 Bug \"{$bug->title}\" sudah selesai diperbaiki oleh Developer ({$user->name}). Silakan lakukan retest.",
                'is_read' => false,
            ]);

            // Juga beri tahu Admin
            $admins = User::where('role', 'Admin')->get();
            foreach ($admins as $admin) {
                BugNotification::create([
                    'user_id' => $admin->id,
                    'bug_id'  => $bug->id,
                    'type'    => 'bug_done_review',
                    'message' => "📋 Developer {$user->name} menandai bug \"{$bug->title}\" sebagai Done in Review.",
                    'is_read' => false,
                ]);
            }
        }

        // 2. Developer mulai mengerjakan → beri tahu QA
        if ($newStatus === 'In Progress' && $oldStatus === 'Open' && $bug->reported_by) {
            BugNotification::create([
                'user_id' => $bug->reported_by,
                'bug_id'  => $bug->id,
                'type'    => 'bug_in_progress',
                'message' => "⚙️ Bug \"{$bug->title}\" sedang dalam pengerjaan oleh Developer ({$user->name}).",
                'is_read' => false,
            ]);
        }

        // 3. QA me-reopen bug → beri tahu Developer
        if ($newStatus === 'Reopened' && $bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'bug_id'  => $bug->id,
                'type'    => 'bug_reopened',
                'message' => "⚠️ Bug \"{$bug->title}\" di-reopen oleh QA setelah retest gagal. Perlu diperbaiki ulang.",
                'is_read' => false,
            ]);
        }

        // 4. QA menutup bug (Closed) → beri tahu Developer
        if ($newStatus === 'Closed' && $bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'bug_id'  => $bug->id,
                'type'    => 'bug_closed',
                'message' => "✅ Bug \"{$bug->title}\" telah ditutup (Closed) oleh QA setelah retest berhasil.",
                'is_read' => false,
            ]);
        }

        if (!$request->expectsJson()) {
            return back()->with('success', 'Status bug berhasil diperbarui!');
        }

        return response()->json([
            'message' => 'Status bug berhasil diperbarui!',
            'data'    => $bug->fresh(),
        ]);
    }
}