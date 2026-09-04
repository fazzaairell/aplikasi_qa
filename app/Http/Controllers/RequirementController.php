<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Project;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::withCount('requirements')->get();
        $selectedProjectId = $request->get('project_id');

        $requirements = collect();
        if ($selectedProjectId) {
            $requirements = Requirement::with('testCases')
                ->where('project_id', $selectedProjectId)
                ->orderBy('code', 'asc')
                ->get();
        }

        return view('requirements.index', compact('projects', 'requirements', 'selectedProjectId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'code' => 'required|string|max:50',
            'description' => 'required|string',
        ]);

        Requirement::create($request->all());

        return redirect()->route('requirements.index', ['project_id' => $request->project_id])
                         ->with('success', 'Requirement berhasil ditambahkan.');
    }

    public function update(Request $request, Requirement $requirement)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'description' => 'required|string',
        ]);

        $requirement->update($request->only(['code', 'description']));

        return redirect()->route('requirements.index', ['project_id' => $requirement->project_id])
                         ->with('success', 'Requirement berhasil diperbarui.');
    }

    public function destroy(Requirement $requirement)
    {
        $projectId = $requirement->project_id;
        $requirement->delete();

        return redirect()->route('requirements.index', ['project_id' => $projectId])
                         ->with('success', 'Requirement berhasil dihapus.');
    }
}