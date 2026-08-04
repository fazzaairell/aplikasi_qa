<?php

namespace App\Http\Controllers;

use App\Models\TestRun;
use App\Models\TestResult;
use App\Models\TestCase;
use App\Models\Bug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class TestRunController extends Controller
{
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

        $testSuites = $request->project_id ? \App\Models\TestSuite::where('project_id', $request->project_id)->with('testCases')->get() : [];
        
        foreach ($testSuites as $suite) {
            foreach ($suite->testCases as $testCase) {
                TestResult::create([
                    'test_run_id' => $testRun->id,
                    'test_case_id' => $testCase->id,
                    'status' => 'Untested',
                    'executed_by' => Auth::id() ?? 1, // <-- 2. Ganti jadi Auth::id()
                ]);
            }
        }

        return response()->json([
            'message' => 'Test Run berhasil dimulai dan test cases berhasil dimuat!',
            'data' => $testRun
        ], 201);
    }

    // 3. Tambahkan tipe data int pada parameter $id
    public function show(int $id)
    {
        $testRun = TestRun::with(['project', 'testResults.testCase', 'testResults.executor', 'testResults.bugs'])->findOrFail($id);

        return response()->json([
            'data' => $testRun
        ]);
    }

    // 4. Tambahkan tipe data int pada parameter $testResultId
    public function updateResult(Request $request, int $testResultId)
    {
        $request->validate([
            'status' => 'required|in:Passed,Failed,Blocked',
            'bug_title' => 'required_if:status,Failed|string|max:255',
            'bug_description' => 'required_if:status,Failed|string',
            'assigned_to' => 'required_if:status,Failed|exists:users,id',
        ]);

        $testResult = TestResult::findOrFail($testResultId);
        
        $testResult->update([
            'status' => $request->status,
            'executed_by' => Auth::id() ?? 1, // <-- 2. Ganti jadi Auth::id()
        ]);

        $bug = null;
        if ($request->status === 'Failed') {
            $bug = Bug::create([
                'test_result_id' => $testResult->id,
                'title' => $request->bug_title,
                'description' => $request->bug_description,
                'status' => 'Open', 
                'assigned_to' => $request->assigned_to, 
            ]);
        }

        return response()->json([
            'message' => 'Hasil tes berhasil diperbarui!',
            'test_result' => $testResult,
            'bug_ticket' => $bug
        ]);
    }
}