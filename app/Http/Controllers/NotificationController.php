<?php

namespace App\Http\Controllers;

use App\Models\BugNotification;
use App\Models\Bug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Halaman timeline notifikasi
     */
    public function timeline(Request $request)
    {
        $user = Auth::user();

        $query = BugNotification::with(['bug.testResult.testCase.testSuite.project', 'user'])
            ->where('user_id', $user->id)
            ->latest('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('read_status')) {
            if ($request->read_status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->read_status === 'read') {
                $query->where('is_read', true);
            }
        }

        $notifications = $query->paginate(20);
        $unreadCount   = BugNotification::where('user_id', $user->id)->where('is_read', false)->count();

        $types = [
            'bug_reported'    => '🐛 Bug Dilaporkan',
            'bug_in_progress' => '⚙️ Bug Sedang Dikerjakan',
            'bug_done_review' => '🔔 Bug Siap Direview',
            'bug_reopened'    => '⚠️ Bug Di-reopen',
            'bug_resolved'    => '✅ Bug Diselesaikan',
            'bug_closed'      => '🎉 Bug Ditutup',
        ];

        return view('notifications.timeline', compact('notifications', 'unreadCount', 'types'));
    }

    /**
     * Tandai satu notifikasi sebagai dibaca → redirect ke bug terkait
     */
    public function markRead(int $id)
    {
        $notif = BugNotification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notif->update(['is_read' => true]);

        // Redirect ke halaman bug (bukan test run) — lebih relevan untuk semua role
        if ($notif->bug_id) {
            return redirect()->route('bugs.show', $notif->bug_id);
        }

        return redirect()->route('notifications.timeline')
            ->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    public function markAllRead()
    {
        BugNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
