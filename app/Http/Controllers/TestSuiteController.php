<?php

namespace App\Http\Controllers;

use App\Models\TestSuite;
use App\Models\TestCase;
use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Http\Request;

class TestSuiteController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua proyek untuk tab navigasi atas
        $projects = Project::all();
        
        // Tentukan proyek aktif (default ke proyek pertama jika tidak ada yang dipilih)
        $selectedProjectId = $request->get('project_id', $projects->first()?->id);
        
        // Ambil Test Suites berdasarkan proyek beserta relasi Test Cases, Requirement, dan Test Results
        $testSuites = TestSuite::with(['testCases.requirement', 'testCases.testResults'])
            ->where('project_id', $selectedProjectId)
            ->get();

        // Ambil Requirements milik proyek aktif untuk dropdown di modal
        $requirements = Requirement::where('project_id', $selectedProjectId)->get();

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
            'test_suite_id' => 'required|exists:test_suites,id',
            'requirement_id' => 'nullable|exists:requirements,id',
            'title' => 'required|string|max:255',
            'steps' => 'required|string',
            'expected_result' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
        ]);

        TestCase::create($request->all());

        $suite = TestSuite::findOrFail($request->test_suite_id);
        return redirect()->route('test-suites.index', ['project_id' => $suite->project_id])
                         ->with('success', 'Test Case berhasil ditambahkan.');
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