<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TestRun;
use App\Models\Bug;
use App\Models\TestCase;
use App\Models\Requirement;
use App\Models\TestSuite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk Admin
     */
    public function index()
    {
        $totalProjects = Project::count();
        $totalCases = TestCase::count();

        // Pass Rate — dihitung dari tabel test_results (status konsisten "Passed", huruf besar)
        $passedCount  = DB::table('test_results')->where('status', 'Passed')->count();
        $totalResults = DB::table('test_results')->count();
        $passRate = $totalResults > 0 ? round(($passedCount / $totalResults) * 100) : 0;

        // Blocked dihitung dari test_results, bukan test_runs (test run cuma punya status Active/Completed)
        $blockedCount = DB::table('test_results')->where('status', 'Blocked')->count();

        // Bug aktif
        $activeBugs = Bug::whereIn('status', ['Open', 'In Progress', 'Reopened'])->count();

        // Test run yang masih berjalan
        $activeTestRuns = TestRun::where('status', 'Active')->latest()->take(2)->get();

        // List untuk ditampilkan
        $recentBugs = Bug::latest()->take(4)->get();
        $projects = Project::withCount('testCases')->latest()->get();

        return view('dashboard.dashboard', compact(
            'totalProjects', 'totalCases', 'passRate', 'activeBugs', 'blockedCount',
            'activeTestRuns', 'recentBugs', 'projects'
        ));
    }

    /**
     * Dashboard untuk QA (Lead & Tester)
     */
    public function qa(Request $request)
    {
        $projects = Project::all();

        $query = DB::table('test_results');
        if ($request->project_id) {
            $query->join('test_cases', 'test_results.test_case_id', '=', 'test_cases.id')
                  ->join('test_suites', 'test_cases.test_suite_id', '=', 'test_suites.id')
                  ->where('test_suites.project_id', $request->project_id)
                  ->select('test_results.*');
        }
        $testResults = $query->get();

        $passed   = $testResults->where('status', 'Passed')->count();
        $failed   = $testResults->where('status', 'Failed')->count();
        $blocked  = $testResults->where('status', 'Blocked')->count();
        $untested = $testResults->where('status', 'Untested')->count();
        $total    = $testResults->count();

        // Data untuk card navigasi — ikut difilter berdasarkan project_id kalau dipilih
        $totalProjects = Project::count();

        $totalRequirements = Requirement::when($request->project_id, function ($q) use ($request) {
            $q->where('project_id', $request->project_id);
        })->count();

        $totalTestSuites = TestSuite::when($request->project_id, function ($q) use ($request) {
            $q->where('project_id', $request->project_id);
        })->count();

        $totalTestRuns = TestRun::when($request->project_id, function ($q) use ($request) {
            $q->where('project_id', $request->project_id);
        })->count();

        $recentRuns = TestRun::with('project')->latest()->take(5)->get();

        return view('dashboard.qa', compact(
            'passed', 'failed', 'blocked', 'untested', 'total', 'recentRuns', 'projects',
            'totalRequirements', 'totalTestSuites', 'totalTestRuns', 'totalProjects'
        ));
    }

    /**
     * Dashboard untuk Developer
     */
    public function developer()
    {
        $user = Auth::user();

        $bugs = Bug::where('assigned_to', $user->id)
            ->with('testResult.testCase.testSuite.project')
            ->latest()
            ->get();

        $totalProjects     = Project::count();
        $totalRequirements = Requirement::count();
        $totalTestSuites   = TestSuite::count();
        $activeBugs        = Bug::whereIn('status', ['Open', 'In Progress'])->count();

        return view('dashboard.developer', compact(
            'bugs', 'totalProjects', 'totalRequirements', 'totalTestSuites', 'activeBugs'
        ));
    }
}