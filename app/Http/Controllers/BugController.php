<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use Illuminate\Http\Request;

class BugController extends Controller
{
    /**
     * Menampilkan daftar bug di halaman Web (Blade).
     */
    public function index()
    {
        // Eager loading relasi agar performa lebih cepat
        $bugs = Bug::with(['testResult.testCase.testSuite.project', 'testResult.testCase.requirement', 'assignee', 'testResult.testRun'])
                   ->latest()
                   ->get();

        return view('bugs.index', compact('bugs'));
    }

    /**
     * API: Mengambil semua daftar bug (format JSON).
     */
    public function apiIndex()
    {
        $bugs = Bug::with(['testResult.testCase.testSuite.project', 'testResult.testCase.requirement', 'assignee', 'testResult.testRun'])->get();

        return response()->json([
            'data' => $bugs
        ]);
    }

    /**
     * Menampilkan detail satu bug (Opsional, berguna untuk modal atau halaman detail).
     */
    public function show(int $id)
    {
        $bug = Bug::with(['testResult.testCase.testSuite.project', 'testResult.testCase.requirement', 'assignee', 'testResult.testRun'])->findOrFail($id);
        
        return view('bugs.show', compact('bug'));
    }

    /**
     * Memperbarui status Bug.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:in Progress,Done in Review',
        ]);

        $bug = Bug::findOrFail($id);

        // Perbarui status bug
        $bug->update([
            'status' => $request->status,
        ]);

        /**
         * Sinkronisasi dengan Flowchart:
         * Jika bug di-Closed (Fix Berhasil oleh QA), 
         * maka Test Result terkait otomatis berubah statusnya menjadi 'Passed'.
         */
        if ($request->status === 'Closed' && $bug->testResult) {
            $bug->testResult->update([
                'status' => 'Passed'
            ]);
        }

        // Jika request berasal dari form web biasa, redirect kembali
        if (!$request->expectsJson()) {
            return back()->with('success', 'Status bug berhasil diperbarui dan hasil tes disesuaikan!');
        }

        // Jika request berasal dari API/AJAX, kembalikan JSON
        return response()->json([
            'message' => 'Status bug berhasil diperbarui!',
            'data' => $bug
        ]);
    }
}