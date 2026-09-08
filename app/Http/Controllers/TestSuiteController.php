<?php

namespace App\Http\Controllers;

use App\Models\TestSuite;
use App\Models\TestCase;
use App\Models\TestCaseStep;
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

        if ($selectedProjectId) {
            $testSuites = TestSuite::with([
                'testCases.requirement',
                'testCases.testResults',
                'testCases.subSteps',
            ])
                ->where('project_id', $selectedProjectId)
                ->get();

            $requirements = Requirement::where('project_id', $selectedProjectId)
                ->get()
                ->sortBy('code', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();
        }

        return view('test-suites.index', compact('projects', 'testSuites', 'requirements', 'selectedProjectId'));
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
            'test_suite_id'   => 'required|exists:test_suites,id',
            'requirement_id'  => 'nullable|exists:requirements,id',
            'test_case_code'  => 'nullable|string|max:50',
            'title'           => 'required|string|max:255',
            'steps'           => 'nullable|string',        // opsional — detail ada di sub-steps
            'expected_result' => 'nullable|string',
            'priority'        => 'required|in:Low,Medium,High,Critical',
        ]);

        TestCase::create($request->only([
            'test_suite_id',
            'requirement_id',
            'test_case_code',
            'title',
            'steps',
            'expected_result',
            'priority',
        ]));

        $suite = TestSuite::findOrFail($request->test_suite_id);
        return redirect()->route('test-suites.index', ['project_id' => $suite->project_id])
                         ->with('success', 'Test Case berhasil ditambahkan.');
    }

    public function storeSubStep(Request $request)
    {
        $request->validate([
            'test_case_id' => 'required|exists:test_cases,id',
            'description' => 'required|string',
            'expected_result' => 'nullable|string',
        ]);

        $testCase = TestCase::findOrFail($request->test_case_id);

        // Nomor langkah dibuat otomatis mengikuti urutan (max + 1), tidak lagi diinput manual.
        $nextStepNumber = ((int) $testCase->subSteps()->max('step_number')) + 1;

        $step = $testCase->subSteps()->create([
            'step_number' => $nextStepNumber,
            'description' => $request->description,
            'expected_result' => $request->expected_result,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Langkah uji berhasil ditambahkan.',
                'step' => $step,
            ]);
        }

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

    public function updateCase(Request $request, int $id)
    {
        $request->validate([
            'requirement_id'  => 'nullable|exists:requirements,id',
            'test_case_code'  => 'nullable|string|max:50',
            'title'           => 'required|string|max:255',
            'expected_result' => 'nullable|string',
            'priority'        => 'required|in:Low,Medium,High,Critical',
        ]);

        $testCase = TestCase::findOrFail($id);
        $testCase->update($request->only([
            'requirement_id',
            'test_case_code',
            'title',
            'expected_result',
            'priority',
        ]));

        $suite = $testCase->testSuite;
        return redirect()->route('test-suites.index', ['project_id' => $suite->project_id])
                         ->with('success', 'Test Case berhasil diperbarui.');
    }
}