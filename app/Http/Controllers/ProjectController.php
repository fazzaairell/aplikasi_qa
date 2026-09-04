<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // ============== WEB (sudah ada) ==============

    public function index()
    {
        $projects = Project::with(['requirements', 'testSuites.testCases'])->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|max:50',
            'test_plan'   => 'nullable|array',
        ]);

        Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status ?? 'Aktif',
            'test_plan'   => $request->test_plan,
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil ditambahkan!');
    }

    public function show(Project $project)
    {
        $project->load(['requirements', 'testSuites.testCases']);
        return view('projects.show', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|max:50',
            'test_plan'   => 'nullable|array',
        ]);

        $project->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status ?? $project->status,
            'test_plan'   => $request->test_plan,
        ]);

        return redirect()->route('projects.show', $project->id)->with('success', 'Proyek berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus!');
    }

    public function requirementsJson(int $id)
    {
        $requirements = Requirement::where('project_id', $id)
            ->orderBy('code')
            ->get(['id', 'code', 'description']);

        return response()->json($requirements);
    }

    // ============== API MOBILE (baru ditambahkan) ==============

    /**
     * API: daftar semua proyek (untuk mobile)
     */
    public function apiIndex()
    {
        $projects = Project::with(['requirements', 'testSuites.testCases'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $projects,
        ]);
    }

    /**
     * API: detail 1 proyek (untuk mobile)
     */
    public function apiShow(Project $project)
    {
        $project->load(['requirements', 'testSuites.testCases']);

        return response()->json([
            'status' => 'success',
            'data'   => $project,
        ]);
    }
}