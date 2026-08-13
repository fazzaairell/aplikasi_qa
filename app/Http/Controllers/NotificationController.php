<?php

namespace App\Http\Controllers;

use App\Models\BugNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     * Redirect ke test run terkait bug tersebut.
     */
    public function markRead(int $id)
    {
        $notif = BugNotification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notif->update(['is_read' => true]);

        // Arahkan ke test run yang menghasilkan bug ini
        $testRunId = $notif->bug?->testResult?->test_run_id;

        if ($testRunId) {
            return redirect()->route('test-runs.index', ['highlight' => $testRunId])
                ->with('info', 'Silakan lakukan retest pada test run terkait.');
        }

        return redirect()->route('bugs.index')
            ->with('info', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi milik user yang login sebagai dibaca.
     */
    public function markAllRead()
    {
        BugNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
