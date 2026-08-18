<?php

use App\Models\Project;
use App\Models\Requirement;
use App\Models\TestCase;
use App\Models\TestSuite;
use App\Models\MasterTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('master test case can be reused by a test suite and a test case can store sub steps', function () {
    $project = Project::create([
        'name' => 'Module 2 Project',
        'description' => 'Untuk validasi modul 2',
        'status' => 'Aktif',
        'test_plan' => 'Scope: login',
    ]);

    $requirement = Requirement::create([
        'project_id' => $project->id,
        'code' => 'REQ-01',
        'description' => 'User login',
    ]);

    $suite = TestSuite::create([
        'project_id' => $project->id,
        'name' => 'Authentication Suite',
    ]);

    $masterTestCase = MasterTestCase::create([
        'project_id' => $project->id,
        'requirement_id' => $requirement->id,
        'title' => 'Login with valid credentials',
        'steps' => '1. Open login form\n2. Enter valid data',
        'expected_result' => 'User is redirected to dashboard',
        'priority' => 'High',
    ]);

    $suite->masterTestCases()->attach($masterTestCase->id);

    $testCase = TestCase::create([
        'test_suite_id' => $suite->id,
        'requirement_id' => $requirement->id,
        'title' => 'Login with valid credentials',
        'steps' => '1. Open login form',
        'expected_result' => 'User is redirected to dashboard',
        'priority' => 'High',
    ]);

    $testCase->subSteps()->create([
        'step_number' => 1,
        'description' => 'Open login page',
        'expected_result' => 'Login page displayed',
    ]);

    expect($suite->masterTestCases)->toHaveCount(1)
        ->and($testCase->subSteps)->toHaveCount(1)
        ->and($testCase->subSteps->first()->description)->toBe('Open login page');
});
