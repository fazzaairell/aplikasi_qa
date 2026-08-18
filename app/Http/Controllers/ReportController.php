<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\BugHistory;
use App\Models\Project;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman riwayat/report bug dengan timeline.
     */
    public function bugHistory(Request $request)
    {
        $query = BugHistory::with(['bug', 'changedBy'])
            ->latest('created_at');

        // Filter by bug status
        if ($request->filled('bug_status')) {
            $bugIds = Bug::where('status', $request->bug_status)->pluck('id');
            $query->whereIn('bug_id', $bugIds);
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $bugIds = Bug::whereHas('testResult.testCase.testSuite', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            })->pluck('id');
            $query->whereIn('bug_id', $bugIds);
        }

        // Filter by assigned_to
        if ($request->filled('assigned_to')) {
            $bugIds = Bug::where('assigned_to', $request->assigned_to)->pluck('id');
            $query->whereIn('bug_id', $bugIds);
        }

        // Filter by field name (status, assigned_to, etc)
        if ($request->filled('field_name')) {
            $query->where('field_name', $request->field_name);
        }

        $histories = $query->paginate(20);
        
        // Get projects and statuses for filter dropdown
        $projects = Project::all();
        $statuses = ['Open', 'In Progress', 'Done in Review', 'Resolved', 'Closed', 'Reopened'];

        return view('reports.bug-history', compact('histories', 'projects', 'statuses'));
    }

    /**
     * Menampilkan detail riwayat satu bug.
     */
    public function bugDetail(int $bugId)
    {
        $bug = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'reporter',
            'histories.changedBy',
        ])->findOrFail($bugId);

        return view('reports.bug-detail', compact('bug'));
    }
}
