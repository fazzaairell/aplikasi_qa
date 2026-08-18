<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\TestRun;
use App\Models\TestResult;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardReportController extends Controller
{
    /**
     * Menampilkan halaman comprehensive reports
     */
    public function reports(Request $request)
    {
        // Filter by project
        $projectQuery = Project::query();
        if ($request->filled('project_id')) {
            $projectQuery->where('id', $request->project_id);
        }
        $projects = Project::all();
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        // Bug Statistics
        $bugQuery = Bug::query();
        if ($request->filled('project_id')) {
            $bugQuery->whereHas('testResult.testCase.testSuite', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        $totalBugs = $bugQuery->count();
        $bugsByStatus = [
            'Open' => $bugQuery->clone()->where('status', 'Open')->count(),
            'In Progress' => $bugQuery->clone()->where('status', 'In Progress')->count(),
            'Done in Review' => $bugQuery->clone()->where('status', 'Done in Review')->count(),
            'Closed' => $bugQuery->clone()->where('status', 'Closed')->count(),
            'Reopened' => $bugQuery->clone()->where('status', 'Reopened')->count(),
        ];

        // Test Run Statistics
        $runQuery = TestRun::query();
        if ($request->filled('project_id')) {
            $runQuery->where('project_id', $request->project_id);
        }

        $totalRuns = $runQuery->count();
        $activeRuns = $runQuery->clone()->where('status', 'Active')->count();
        $completedRuns = $runQuery->clone()->where('status', 'Completed')->count();

        // Test Result Statistics
        $resultQuery = TestResult::query();
        if ($request->filled('project_id')) {
            $resultQuery->whereHas('testRun', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        $totalResults = $resultQuery->count();
        $resultsByStatus = [
            'Passed' => $resultQuery->clone()->where('status', 'Passed')->count(),
            'Failed' => $resultQuery->clone()->where('status', 'Failed')->count(),
            'Blocked' => $resultQuery->clone()->where('status', 'Blocked')->count(),
            'Untested' => $resultQuery->clone()->where('status', 'Untested')->count(),
        ];

        // Pass Rate
        $passRate = $totalResults > 0 ? round(($resultsByStatus['Passed'] / $totalResults) * 100) : 0;

        // Average bugs per run
        $avgBugsPerRun = $totalRuns > 0 ? round($totalBugs / $totalRuns, 2) : 0;

        // Recent bugs
        $recentBugs = Bug::when($request->filled('project_id'), function ($q) use ($request) {
            $q->whereHas('testResult.testCase.testSuite', function ($subQ) use ($request) {
                $subQ->where('project_id', $request->project_id);
            });
        })->latest()->take(5)->get();

        // Recent test runs
        $recentRuns = TestRun::when($request->filled('project_id'), function ($q) use ($request) {
            $q->where('project_id', $request->project_id);
        })->with('project')->latest()->take(5)->get();

        return view('reports.comprehensive', compact(
            'projects', 'selectedProject',
            'totalBugs', 'bugsByStatus',
            'totalRuns', 'activeRuns', 'completedRuns',
            'totalResults', 'resultsByStatus', 'passRate',
            'avgBugsPerRun', 'recentBugs', 'recentRuns'
        ));
    }
}
