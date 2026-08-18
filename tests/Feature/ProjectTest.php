<?php

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('project can store status and test plan data', function () {
    $project = Project::create([
        'name' => 'Portal QA',
        'description' => 'Platform untuk pengujian',
        'status' => 'Aktif',
        'test_plan' => "Scope: Registrasi\nJadwal: Agustus 2026\nPIC: QA Lead\nStrategi: smoke + regression",
    ]);

    expect($project->status)->toBe('Aktif')
        ->and($project->test_plan)->toContain('Scope')
        ->and($project->name)->toBe('Portal QA');
});
