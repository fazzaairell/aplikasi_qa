<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBugRequest;
use App\Http\Requests\UpdateBugStatusRequest;
use App\Models\Bug;
use App\Models\BugNotification;
use App\Models\Project;
use App\Models\User;
use App\Services\BugStatusService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected BugStatusService $bugStatusService,
    ) {
    }

    /**
     * Daftar bug aktif (project status != Selesai)
     */
    public function index(Request $request)
    {
        $bugs = $this->filteredBugsQuery($request, projectDone: false)->latest()->get();

        return view('bugs.index', [
            'bugs' => $bugs,
            'isHistory' => false,
            'projects' => Project::where('status', '!=', 'Selesai')->get(),
            'developers' => User::where('role', 'Developer')->get(),
        ]);
    }

    /**
     * Riwayat bug dari project yang sudah Selesai
     */
    public function history(Request $request)
    {
        $bugs = $this->filteredBugsQuery($request, projectDone: true)->latest()->get();

        return view('bugs.index', [
            'bugs' => $bugs,
            'isHistory' => true,
            'projects' => Project::where('status', '=', 'Selesai')->get(),
            'developers' => User::where('role', 'Developer')->get(),
        ]);
    }

    /**
     * Query dasar daftar bug + filter yang dipakai bareng oleh index() & history().
     * Bedanya cuma status project (aktif vs sudah Selesai) dan filter tambahan dari request.
     */
    protected function filteredBugsQuery(Request $request, bool $projectDone)
    {
        $query = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'reporter',
            'testResult.testRun',
        ])->whereHas('testResult.testCase.testSuite.project', function ($q) use ($projectDone) {
            $q->where('status', $projectDone ? '=' : '!=', 'Selesai');
        });

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }
        if ($request->filled('project_id')) {
            $query->whereHas('testResult.testCase.testSuite.project', fn ($q) =>
                $q->where('id', $request->project_id)
            );
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    /**
     * Buat bug baru secara standalone (tanpa test result)
     */
    public function store(StoreBugRequest $request)
    {
        $data = $request->validated();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->fileUploadService->store($request->file('attachment'), 'bug-attachments');
        }

        $bug = Bug::create([
            'test_result_id'  => null,
            'title'           => $data['title'],
            'description'     => $data['description'],
            'expected_result' => $data['expected_result'] ?? null,
            'status'          => Bug::STATUS_OPEN,
            'assigned_to'     => $data['assigned_to'],
            'reported_by'     => Auth::id(),
            'due_date'        => $data['due_date'],
            'attachment'      => $attachmentPath,
        ]);

        $this->notifyNewBug($bug);

        return back()->with('success', 'Bug berhasil dilaporkan!');
    }

    /**
     * Notifikasi ke Developer yang di-assign + semua Admin saat bug baru dilaporkan.
     */
    protected function notifyNewBug(Bug $bug): void
    {
        $reporter = Auth::user();

        if ($bug->assigned_to) {
            BugNotification::create([
                'user_id' => $bug->assigned_to,
                'bug_id'  => $bug->id,
                'type'    => 'bug_reported',
                'message' => "🐛 Bug baru dilaporkan oleh {$reporter->name}: \"{$bug->title}\". Segera ditangani!",
                'is_read' => false,
            ]);
        }

        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            BugNotification::create([
                'user_id' => $admin->id,
                'bug_id'  => $bug->id,
                'type'    => 'bug_reported',
                'message' => "📋 Bug baru: \"{$bug->title}\" dilaporkan oleh {$reporter->name}.",
                'is_read' => false,
            ]);
        }
    }

    /**
     * Detail satu bug
     */
    public function show(int $id)
    {
        $bug = Bug::with([
            'testResult.testCase.testSuite.project',
            'testResult.testCase.requirement',
            'assignee',
            'reporter',
            'testResult.testRun',
        ])->findOrFail($id);

        return view('bugs.show', compact('bug'));
    }

    /**
     * Update status bug sesuai workflow QA.
     *
     * Developer:
     * Open/Reopened -> In Progress -> Done in Review
     *
     * QA Lead / QA Tester / Admin:
     * Done in Review -> Resolved atau Reopened
     *
     * Developer hanya boleh mengubah bug yang ditugaskan kepadanya.
     * Logic transisi & notifikasi ada di BugStatusService.
     */
    public function updateStatus(UpdateBugStatusRequest $request, int $id)
    {
        $bug = Bug::with(['testResult', 'assignee', 'reporter'])->findOrFail($id);

        $result = $this->bugStatusService->updateStatus(
            $bug,
            Auth::user(),
            $request->validated('status'),
            $request->file('fix_attachment')
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $result->message,
                'data' => $result->bug,
            ], $result->statusCode);
        }

        return back()->with($result->success ? 'success' : 'error', $result->message);
    }
}
