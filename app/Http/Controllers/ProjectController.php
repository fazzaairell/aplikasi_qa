<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Menampilkan daftar proyek
    public function index()
    {
        $projects = Project::with(['requirements', 'testSuites.testCases'])->latest()->get();
        return view('projects.index', compact('projects'));
    }

    // Menyimpan proyek baru
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => 'Aktif',
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil ditambahkan!');
    }

    // Menampilkan halaman detail proyek (BARU)
    public function show(Project $project)
    {
        $project->load(['requirements', 'testSuites.testCases']);
        return view('projects.show', compact('project'));
    }

    // Memperbarui data proyek
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
        ]);

        $project->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status ?? $project->status,
        ]);

        return redirect()->route('projects.show', $project->id)->with('success', 'Proyek berhasil diperbarui!');
    }

    // Menghapus data proyek
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus!');
    }
}