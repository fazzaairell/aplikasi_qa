<?php

namespace App\Http\Controllers;

use App\Models\TestRun;
use App\Models\TestResult;
use App\Models\TestCase;
use App\Models\Bug;
use App\Models\Project;
use App\Models\TestSuite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class TestRunController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::all();
        $users = User::all();
        $selectedProjectId = $request->input('project_id', $projects->first()?->id);

        $testRuns = TestRun::with(['project', 'testResults.testCase', 'testResults.bugs', 'testResults.executor'])
            ->when($selectedProjectId, function ($query, $selectedProjectId) {
                return $query->where('project_id', $selectedProjectId);
            })
            ->latest()
            ->get();

        return view('test-runs.index', compact('projects', 'selectedProjectId', 'testRuns', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
        ]);

        $testRun = TestRun::create([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'status' => 'Active',
        ]);

        $testSuites = TestSuite::where('project_id', $request->project_id)->with('testCases')->get();
        
        foreach ($testSuites as $suite) {
            foreach ($suite->testCases as $testCase) {
                TestResult::create([
                    'test_run_id' => $testRun->id,
                    'test_case_id' => $testCase->id,
                    'status' => 'Untested',
                    'executed_by' => Auth::id() ?? 1,
                ]);
            }
        }

        return response()->json([
            'message' => 'Test Run berhasil dimulai dan test cases berhasil dimuat!',
            'data' => $testRun
        ], 201);
    }

    public function show(int $id)
    {
        $testRun = TestRun::with(['project', 'testResults.testCase', 'testResults.executor', 'testResults.bugs'])->findOrFail($id);

        return response()->json([
            'data' => $testRun
        ]);
    }

    public function updateResult(Request $request, int $testResultId)
    {
        $request->validate([
            'status' => 'required|in:Passed,Failed,Blocked,Untested',
            'bug_title' => 'required_if:status,Failed|nullable|string|max:255',
            'bug_description' => 'required_if:status,Failed|nullable|string',
            'assigned_to' => 'required_if:status,Failed|nullable|exists:users,id',
            'due_date' => 'required_if:status,Failed|nullable|date',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $testResult = TestResult::findOrFail($testResultId);
        
        $testResult->update([
            'status' => $request->status,
            'executed_by' => Auth::id() ?? 1,
        ]);

        $bug = null;
        if ($request->status === 'Failed') {
            $attachmentPath = null;
            
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('bug-attachments', 'public');
            }

            $bug = Bug::create([
                'test_result_id' => $testResult->id,
                'title' => $request->bug_title,
                'description' => $request->bug_description,
                'status' => 'in Progress', 
                'assigned_to' => $request->assigned_to, 
                'due_date' => $request->due_date,
                'attachment' => $attachmentPath,
            ]);
        }

        return response()->json([
            'message' => 'Hasil tes berhasil diperbarui!',
            'test_result' => $testResult,
            'bug_ticket' => $bug
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:Active,Completed',
        ]);

        $testRun = TestRun::findOrFail($id);
        $testRun->update([
            'title' => $request->title,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Test Run berhasil diperbarui!',
            'data' => $testRun
        ]);
    }

    public function destroy(int $id)
    {
        $testRun = TestRun::findOrFail($id);
        $testRun->testResults()->delete();
        $testRun->delete();

        return response()->json([
            'message' => 'Test Run berhasil dihapus!'
        ]);
    }
}