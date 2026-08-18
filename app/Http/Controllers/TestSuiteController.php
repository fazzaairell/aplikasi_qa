<?php

namespace App\Http\Controllers;

use App\Models\TestSuite;
use App\Models\TestCase;
use App\Models\TestCaseStep;
use App\Models\MasterTestCase;
use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Http\Request;

class TestSuiteController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::withCount('testSuites')->get();
        $selectedProjectId = $request->get('project_id');

        $testSuites = collect();
        $requirements = collect();
        $masterTestCases = collect();

        if ($selectedProjectId) {
            $testSuites = TestSuite::with([
                'testCases.requirement',
                'testCases.testResults',
                'testCases.subSteps',
                'masterTestCases.requirement',
            ])
                ->where('project_id', $selectedProjectId)
                ->get();

            $requirements = Requirement::where('project_id', $selectedProjectId)->get();
            $masterTestCases = MasterTestCase::with('requirement', 'testSuites')
                ->where('project_id', $selectedProjectId)
                ->orderBy('title')
                ->get();
        }

        return view('test-suites.index', compact('projects', 'testSuites', 'requirements', 'masterTestCases', 'selectedProjectId'));
    }

    public function masterIndex(Request $request)
    {
        $projects = Project::all();
        $selectedProjectId = $request->get('project_id', $projects->first()?->id);
        $masterTestCases = MasterTestCase::with(['requirement', 'testSuites'])
            ->where('project_id', $selectedProjectId)
            ->latest()
            ->get();
        $suites = TestSuite::where('project_id', $selectedProjectId)->get();

        return view('master-test-cases.index', compact('projects', 'selectedProjectId', 'masterTestCases', 'suites'));
    }

    public function storeSuite(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255'
        ]);

        TestSuite::create($request->only(['project_id', 'name']));

        return redirect()->route('test-suites.index', ['project_id' => $request->project_id])
                         ->with('success', 'Test Suite berhasil ditambahkan.');
    }

    public function destroySuite(int $id)
    {
        $suite = TestSuite::findOrFail($id);
        $projectId = $suite->project_id;
        $suite->delete();

        return redirect()->route('test-suites.index', ['project_id' => $projectId])
                         ->with('success', 'Test Suite berhasil dihapus.');
    }

    public function storeCase(Request $request)
    {
        $request->validate([
            'test_suite_id' => 'required|exists:test_suites,id',
            'requirement_id' => 'nullable|exists:requirements,id',
            'title' => 'required|string|max:255',
            'steps' => 'required|string',
            'expected_result' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
        ]);

        $testCase = TestCase::create($request->all());

        if ($request->filled('master_test_case_id')) {
            $testCase->subSteps()->delete();
            $masterCase = MasterTestCase::findOrFail($request->master_test_case_id);
            $testCase->subSteps()->create([
                'step_number' => 1,
                'description' => $masterCase->steps,
                'expected_result' => $masterCase->expected_result,
            ]);
        }

        $suite = TestSuite::findOrFail($request->test_suite_id);
        return redirect()->route('test-suites.index', ['project_id' => $suite->project_id])
                         ->with('success', 'Test Case berhasil ditambahkan.');
    }

    public function storeMasterCase(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'requirement_id' => 'nullable|exists:requirements,id',
            'title' => 'required|string|max:255',
            'steps' => 'required|string',
            'expected_result' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
        ]);

        $masterCase = MasterTestCase::create($request->all());

        if ($request->filled('test_suite_id')) {
            $masterCase->testSuites()->syncWithoutDetaching([$request->test_suite_id]);
        }

        return redirect()->route('master-test-cases.index', ['project_id' => $request->project_id])
                         ->with('success', 'Master Test Case berhasil disimpan.');
    }

    public function attachMasterCase(Request $request)
    {
        $request->validate([
            'test_suite_id' => 'required|exists:test_suites,id',
            'master_test_case_id' => 'required|exists:master_test_cases,id',
        ]);

        $suite = TestSuite::findOrFail($request->test_suite_id);
        $suite->masterTestCases()->syncWithoutDetaching([$request->master_test_case_id]);

        return redirect()->route('test-suites.index', ['project_id' => $suite->project_id])
                         ->with('success', 'Master Test Case berhasil ditambahkan ke suite.');
    }

    public function storeSubStep(Request $request)
    {
        $request->validate([
            'test_case_id' => 'required|exists:test_cases,id',
            'step_number' => 'required|integer|min:1',
            'description' => 'required|string',
            'expected_result' => 'required|string',
        ]);

        $testCase = TestCase::findOrFail($request->test_case_id);
        $testCase->subSteps()->create($request->only(['step_number', 'description', 'expected_result']));

        return redirect()->route('test-suites.index', ['project_id' => $testCase->test_suite_id ? $testCase->testSuite->project_id : null])
                         ->with('success', 'Langkah uji berhasil ditambahkan.');
    }

    public function destroyCase(int $id)
    {
        $testCase = TestCase::findOrFail($id);
        $suite = $testCase->testSuite;
        $testCase->delete();

        return redirect()->route('test-suites.index', ['project_id' => $suite->project_id])
                         ->with('success', 'Test Case berhasil dihapus.');
    }
}