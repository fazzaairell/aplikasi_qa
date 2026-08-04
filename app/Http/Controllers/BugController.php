<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use Illuminate\Http\Request;

class BugController extends Controller
{
    // 1. Menampilkan semua daftar bug yang masuk
    public function index()
    {
        $bugs = Bug::with(['testResult.testCase', 'assignee', 'testResult.testRun'])->get();

        return response()->json([
            'data' => $bugs
        ]);
    }

    // 2. Memperbarui status Bug (misal: Open -> In Progress -> Resolved -> Closed / Reopened)
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved,Closed,Reopened',
        ]);

        $bug = Bug::findOrFail($id);

        $bug->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Status bug berhasil diperbarui!',
            'data' => $bug
        ]);
    }
}