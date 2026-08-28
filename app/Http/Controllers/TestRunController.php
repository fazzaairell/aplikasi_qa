<?php

namespace App\Http\Controllers;

use App\Models\TestRun;
use App\Models\TestResult;
use App\Models\TestCase;
use App\Models\Bug;
use App\Models\BugNotification;
use App\Models\Project;
use App\Models\TestSuite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestRunController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with('testSuites')->get();
        $users    = User::where('role', 'Developer')->get(); // hanya developer
        $selectedProjectId = $request->input('project_id', $projects->first()?->id);

        $testRuns = TestRun::with(['project', 'testResults.testCase', 'testResults.bugs', 'testResults.executor'])
            ->when($selectedProjectId, fn($q, $id) => $q->where('project_id', $id))
            ->latest()
            ->get();

        return view('test-runs.index', compact('projects', 'selectedProjectId', 'testRuns', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'test_suite_id' => 'nullable|exists:test_suites,id',
            'title'         => 'required|string|max:255',
        ]);

        $testRun = TestRun::create([
            'project_id' => $request->project_id,
            'title'      => $request->title,
            'status'     => 'Active',
        ]);

        $testSuitesQuery = TestSuite::where('project_id', $request->project_id)->with('testCases');
        if ($request->test_suite_id) {
            $testSuitesQuery->where('id', $request->test_suite_id);
        }
        $testSuites = $testSuitesQuery->get();

        foreach ($testSuites as $suite) {
            foreach ($suite->testCases as $testCase) {
                TestResult::create([
                    'test_run_id'  => $testRun->id,
                    'test_case_id' => $testCase->id,
                    'status'       => 'Untested',
                    'executed_by'  => Auth::id() ?? 1,
                ]);
            }
        }

        return response()->json([
            'message' => 'Test Run berhasil dimulai!',
            'data'    => $testRun
        ], 201);
    }

    public function show(int $id)
    {
        $testRun = TestRun::with(['project', 'testResults.testCase', 'testResults.executor', 'testResults.bugs'])
            ->findOrFail($id);

        return response()->json(['data' => $testRun]);
    }

    public function updateResult(Request $request, int $testResultId)
    {

        $request->validate([
            'status'          => 'required|in:Passed,Failed,Blocked,Untested',
            'bug_title'       => 'required_if:status,Failed|nullable|string|max:255',
            'bug_description' => 'required_if:status,Failed|nullable|string',
            'expected_result' => 'nullable|string',
            'assigned_to'     => 'required_if:status,Failed|nullable|exists:users,id',
            'due_date'        => 'required_if:status,Failed|nullable|date',
            'attachment'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);



        $testResult = TestResult::with(['testCase.testSuite.project'])->findOrFail($testResultId);
        $reporter   = Auth::user();
        // ── Update status test result ─────────────────────────────────────
        $testResult->update([
            'status'      => $request->status,
            'executed_by' => Auth::id() ?? 1,
        ]);

        $bug = null;

        // ── Jika Failed → buat Bug baru ───────────────────────────────────
        if ($request->status === 'Failed') {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file      = $request->file('attachment');
                $filename  = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $directory = public_path('uploads/bug-attachments');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $file->move($directory, $filename);
                $attachmentPath = 'bug-attachments/' . $filename;
            }

            $bug = Bug::create([
                'test_result_id'  => $testResult->id,
                'title'           => $request->bug_title,
                'description'     => $request->bug_description,
                'expected_result' => $request->expected_result ?? $testResult->testCase?->expected_result,
                'status'          => 'Open',
                'assigned_to'     => $request->assigned_to,
                'reported_by'     => Auth::id(),
                'due_date'        => $request->due_date,
                'attachment'      => $attachmentPath,
            ]);

            // ── Notifikasi ke Developer yang di-assign ────────────────────
            if ($bug->assigned_to) {
                $projectName  = $testResult->testCase?->testSuite?->project?->name ?? 'Project';
                $reporterName = $reporter?->name ?? 'QA Tester';

                BugNotification::create([
                    'user_id' => $bug->assigned_to,
                    'bug_id'  => $bug->id,
                    'type'    => 'bug_reported',
                    'message' => "🐛 Bug baru dilaporkan oleh {$reporterName} pada project \"{$projectName}\": \"{$bug->title}\". Segera ditangani!",
                    'is_read' => false,
                ]);
            }

            // ── Notifikasi ke Admin tentang bug baru ──────────────────────
            $admins = User::where('role', 'Admin')->get();
            foreach ($admins as $admin) {
                BugNotification::create([
                    'user_id' => $admin->id,
                    'bug_id'  => $bug->id,
                    'type'    => 'bug_reported',
                    'message' => "📋 Bug baru: \"{$bug->title}\" dilaporkan dari Test Run.",
                    'is_read' => false,
                ]);
            }
        }

        // ── Jika Passed → update status bug terkait jadi Closed ──────────
        if ($request->status === 'Passed') {
            $existingBug = Bug::where('test_result_id', $testResult->id)
                ->whereNotIn('status', ['Closed', 'Done'])
                ->first();

            if ($existingBug) {
                $existingBug->update([
                    'status'      => 'Closed',
                    'finish_date' => now()->toDateString(),
                ]);

                // Notifikasi ke QA yang melaporkan bahwa bug selesai via retest
                if ($existingBug->reported_by) {
                    BugNotification::create([
                        'user_id' => $existingBug->reported_by,
                        'bug_id'  => $existingBug->id,
                        'type'    => 'bug_resolved',
                        'message' => "✅ Bug \"{$existingBug->title}\" telah berhasil di-retest dan dinyatakan Closed.",
                        'is_read' => false,
                    ]);
                }
            }
        }

        // ── Auto-update status Test Run jadi Completed ────────────────────
        $testRun = $testResult->testRun;
        if ($testRun) {
            $stillUntested = $testRun->testResults()->whereNotIn('status', ['Passed', 'Failed', 'Blocked'])->exists();
            if (!$stillUntested) {
                $testRun->update(['status' => 'Completed']);
            }
        }

        return response()->json([
            'message'     => 'Hasil tes berhasil diperbarui!',
            'test_result' => $testResult,
            'bug_ticket'  => $bug,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'status' => 'required|in:Active,Completed',
        ]);

        $testRun = TestRun::findOrFail($id);
        $testRun->update(['title' => $request->title, 'status' => $request->status]);

        return response()->json(['message' => 'Test Run berhasil diperbarui!', 'data' => $testRun]);
    }

    public function destroy(int $id)
    {
        $testRun = TestRun::findOrFail($id);
        $testRun->testResults()->delete();
        $testRun->delete();

        return response()->json(['message' => 'Test Run berhasil dihapus!']);
    }

    public function summary(int $id)
    {
        $testRun = TestRun::with([
            'project',
            'testResults.testCase',
            'testResults.bugs.assignee',
            'testResults.bugs.reporter',
        ])->findOrFail($id);

        $results  = $testRun->testResults;
        $total    = $results->count();
        $passed   = $results->where('status', 'Passed')->count();
        $failed   = $results->where('status', 'Failed')->count();
        $blocked  = $results->where('status', 'Blocked')->count();
        $untested = $results->where('status', 'Untested')->count();
        $passRate = $total > 0 ? round(($passed / $total) * 100) : 0;

        $bugs = $results->flatMap(fn($r) => $r->bugs)->values();

        $blockingBugs = $bugs->filter(function ($bug) {
            $priority = $bug->testResult?->testCase?->priority ?? 'Low';
            return in_array($priority, ['Critical', 'High']) && !in_array($bug->status, ['Closed', 'Done']);
        });

        $isReady = $blockingBugs->isEmpty() && $untested === 0;

        return view('test-runs.summary', compact(
            'testRun', 'total', 'passed', 'failed', 'blocked', 'untested',
            'passRate', 'bugs', 'isReady'
        ));
    }
}