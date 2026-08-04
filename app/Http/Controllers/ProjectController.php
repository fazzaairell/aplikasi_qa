<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Menampilkan daftar proyek yang bisa diakses user
    public function index()
    {
        // Mengambil semua proyek beserta test suites dan test cases di dalamnya
        $projects = Project::with(['requirements', 'testSuites.testCases'])->get();

        // Jika Anda menggunakan Inertia.js untuk React:
        // return Inertia::render('Projects/Index', ['projects' => $projects]);

        // Atau untuk uji coba awal via JSON / API response:
        return response()->json([
            'message' => 'Berhasil mengambil data proyek QA',
            'data' => $projects
        ]);
    }
}