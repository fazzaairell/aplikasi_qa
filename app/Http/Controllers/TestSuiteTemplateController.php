<?php

namespace App\Http\Controllers;

use App\Models\TestSuite;
use App\Models\TestSuiteTemplate;
use App\Models\TestCaseTemplate;
use App\Models\TestCase;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestSuiteTemplateController extends Controller
{
    /**
     * Halaman daftar semua template.
     */
    public function index()
    {
        $templates = TestSuiteTemplate::with('testCaseTemplates', 'creator')
            ->orderByDesc('created_at')
            ->get();

        return view('test-suite-templates.index', compact('templates'));
    }

    /**
     * Simpan sebuah Test Suite yang sudah ada sebagai template baru.
     * Dipanggil dari halaman Test Suites (Langkah 2).
     */
    public function saveAsTemplate(Request $request)
    {
        $request->validate([
            'test_suite_id' => 'required|exists:test_suites,id',
        ]);

        $suite = TestSuite::with('testCases')->findOrFail($request->test_suite_id);

        // Buat template baru (copy nama & deskripsi suite)
        $template = TestSuiteTemplate::create([
            'name'        => $suite->name,
            'description' => $suite->description ?? null,
            'created_by'  => Auth::id(),
        ]);

        // Copy semua test case ke tabel test_case_templates
        foreach ($suite->testCases as $tc) {
            TestCaseTemplate::create([
                'test_suite_template_id' => $template->id,
                'title'                  => $tc->title,
                'steps'                  => $tc->steps,
                'expected_result'        => $tc->expected_result,
                'priority'               => $tc->priority,
            ]);
        }

        return redirect()
            ->route('test-suites.index', ['project_id' => $suite->project_id])
            ->with('success', "Test Suite \"{$suite->name}\" berhasil disimpan sebagai template ({$suite->testCases->count()} test cases ter-copy).");
    }

    /**
     * Rename nama sebuah template.
     */
    public function rename(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $template = TestSuiteTemplate::findOrFail($id);
        $template->update(['name' => $request->name]);

        return redirect()
            ->route('test-suite-templates.index')
            ->with('success', "Template berhasil diubah namanya menjadi \"{$request->name}\".");
    }

    /**
     * Hapus sebuah template (beserta semua test case template-nya via cascade).
     */
    public function destroy(int $id)
    {
        $template = TestSuiteTemplate::findOrFail($id);
        $name = $template->name;
        $template->delete();

        return redirect()
            ->route('test-suite-templates.index')
            ->with('success', "Template \"{$name}\" berhasil dihapus.");
    }

    /**
     * Generate Test Suite baru di project pilihan berdasarkan template.
     * Dipanggil dari modal di halaman Test Suites (Langkah 4).
     */
    public function useTemplate(Request $request)
    {
        $request->validate([
            'template_id'       => 'required|exists:test_suite_templates,id',
            'project_id'        => 'required|exists:projects,id',
            'suite_name'        => 'nullable|string|max:255',
            'requirement_ids'   => 'nullable|array',
            'requirement_ids.*' => 'nullable|exists:requirements,id',
        ]);

        $template = TestSuiteTemplate::with('testCaseTemplates')->findOrFail($request->template_id);

        // Nama suite baru — pakai custom name jika diisi, fallback ke nama template
        $suiteName = $request->suite_name ?: $template->name;

        // Buat Test Suite baru di project tujuan
        $newSuite = TestSuite::create([
            'project_id' => $request->project_id,
            'name'       => $suiteName,
        ]);

        // requirement_ids dikirim sebagai array [tct_id => req_id]
        $requirementIds = $request->input('requirement_ids', []);

        foreach ($template->testCaseTemplates as $tct) {
            $requirementId = isset($requirementIds[$tct->id]) && $requirementIds[$tct->id] !== ''
                ? $requirementIds[$tct->id]
                : null;

            TestCase::create([
                'test_suite_id'   => $newSuite->id,
                'requirement_id'  => $requirementId,
                'title'           => $tct->title,
                'steps'           => $tct->steps ?? '',
                'expected_result' => $tct->expected_result ?? '',
                'priority'        => $tct->priority,
            ]);
        }

        return redirect()
            ->route('test-suites.index', ['project_id' => $request->project_id])
            ->with('success', "Test Suite \"{$suiteName}\" berhasil dibuat dari template \"{$template->name}\" ({$template->testCaseTemplates->count()} test cases ter-generate).");
    }
}
