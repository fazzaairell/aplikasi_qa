<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\BugHistory;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TestCase;
use App\Models\TestResult;
use App\Models\TestRun;
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
        $statuses = Bug::STATUSES;

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

    /**
     * Menampilkan halaman comprehensive reports sebagai
     * Requirement Traceability Matrix (RTM): Project -> Requirement -> Test Case -> riwayat Test Run.
     */
    public function comprehensive(Request $request)
    {
        $projects = Project::all();
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        $requirements = Requirement::with(['project', 'testCases.testResults.testRun'])
            ->when($request->filled('project_id'), function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            })
            ->get()
            // Dikelompokkan per nama project, lalu diurutkan natural per kode requirement (REQ-1, REQ-2, ... REQ-10)
            ->sortBy(function ($r) {
                return ($r->project->name ?? '') . '|' . $r->code;
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        // Urutkan riwayat test result tiap test case dari yang terbaru
        $requirements->each(function ($requirement) {
            $requirement->testCases->each(function ($testCase) {
                $testCase->setRelation(
                    'testResults',
                    $testCase->testResults->sortByDesc(function ($result) {
                        return optional($result->testRun)->created_at ?? $result->created_at;
                    })->values()
                );
            });
        });

        $totalRequirements = $requirements->count();
        $requirementsWithTestCase = $requirements->filter(fn ($r) => $r->testCases->isNotEmpty())->count();
        $totalTestCases = $requirements->sum(fn ($r) => $r->testCases->count());
        $coveragePercent = $totalRequirements > 0
            ? round(($requirementsWithTestCase / $totalRequirements) * 100)
            : 0;

        // Test case yang belum terhubung ke requirement mana pun (misal hasil generate dari template)
        $orphanTestCases = TestCase::whereNull('requirement_id')
            ->with(['testSuite.project', 'testResults.testRun'])
            ->when($request->filled('project_id'), function ($q) use ($request) {
                $q->whereHas('testSuite', function ($subQ) use ($request) {
                    $subQ->where('project_id', $request->project_id);
                });
            })
            ->get()
            ->each(function ($testCase) {
                $testCase->setRelation(
                    'testResults',
                    $testCase->testResults->sortByDesc(function ($result) {
                        return optional($result->testRun)->created_at ?? $result->created_at;
                    })->values()
                );
            });

        return view('reports.comprehensive', compact(
            'projects', 'selectedProject', 'requirements',
            'totalRequirements', 'requirementsWithTestCase', 'totalTestCases', 'coveragePercent',
            'orphanTestCases'
        ));
    }
}
