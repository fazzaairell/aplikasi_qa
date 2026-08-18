<?php

use App\Models\Bug;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TestCase;
use App\Models\TestResult;
use App\Models\TestRun;
use App\Models\TestSuite;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('test run completes automatically when all results are done and bug passes trigger done status', function () {
    $project = Project::create([
        'name' => 'Module 3 Project',
        'description' => 'Project untuk modul 3',
        'status' => 'Aktif',
        'test_plan' => 'Scope: smoke test',
    ]);

    $requirement = Requirement::create([
        'project_id' => $project->id,
        'code' => 'REQ-03',
        'description' => 'Fitur login',
    ]);

    $suite = TestSuite::create([
        'project_id' => $project->id,
        'name' => 'Login Suite',
    ]);

    $testCase = TestCase::create([
        'test_suite_id' => $suite->id,
        'requirement_id' => $requirement->id,
        'title' => 'Login with valid credentials',
        'steps' => '1. Open login page',
        'expected_result' => 'User is redirected to dashboard',
        'priority' => 'High',
    ]);

    $user = User::factory()->create(['role' => 'QA Tester']);
    $developer = User::factory()->create(['role' => 'Developer']);

    $testRun = TestRun::create([
        'project_id' => $project->id,
        'title' => 'Login smoke test',
        'status' => 'Active',
    ]);

    $result = TestResult::create([
        'test_run_id' => $testRun->id,
        'test_case_id' => $testCase->id,
        'status' => 'Failed',
        'executed_by' => $user->id,
    ]);

    $bug = Bug::create([
        'test_result_id' => $result->id,
        'title' => 'Login bug',
        'description' => 'User cannot log in',
        'status' => 'Open',
        'assigned_to' => $developer->id,
        'reported_by' => $user->id,
        'expected_result' => 'User should be redirected to dashboard',
        'attachment' => null,
    ]);

    $result->update(['status' => 'Passed']);
    $bug->refresh();

    $testRun->refresh();
    $testRun->testResults()->update(['status' => 'Passed']);
    $testRun->refresh();

    expect($bug->status)->toBe('Done')
        ->and($testRun->status)->toBe('Completed');
});
