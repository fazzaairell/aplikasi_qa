<?php

use App\Models\Bug;
use App\Models\BugNotification;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TestCase;
use App\Models\TestResult;
use App\Models\TestRun;
use App\Models\TestSuite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('notifications timeline page is accessible and filters work', function () {
    $user = User::factory()->create(['role' => 'QA Tester']);
    
    $this->actingAs($user);
    $response = $this->get(route('notifications.timeline'));
    
    expect($response->status())->toBe(200);
});

test('comprehensive reports page displays all statistics', function () {
    $user = User::factory()->create(['role' => 'QA Tester']);

    $project = Project::create([
        'name' => 'Reports Project',
        'description' => 'Project untuk testing reports',
        'status' => 'Aktif',
        'test_plan' => 'Test reports',
    ]);

    $requirement = Requirement::create([
        'project_id' => $project->id,
        'code' => 'REQ-57',
        'description' => 'Reports feature',
    ]);

    $suite = TestSuite::create([
        'project_id' => $project->id,
        'name' => 'Reports Suite',
    ]);

    $testCase = TestCase::create([
        'test_suite_id' => $suite->id,
        'requirement_id' => $requirement->id,
        'title' => 'Test reports',
        'steps' => 'Test steps',
        'expected_result' => 'Reports work',
        'priority' => 'High',
    ]);

    $testRun = TestRun::create([
        'project_id' => $project->id,
        'title' => 'Reports test run',
        'status' => 'Completed',
    ]);

    $result = TestResult::create([
        'test_run_id' => $testRun->id,
        'test_case_id' => $testCase->id,
        'status' => 'Passed',
        'executed_by' => $user->id,
    ]);

    $bug = Bug::create([
        'test_result_id' => $result->id,
        'title' => 'Reports bug',
        'description' => 'Bug for reports test',
        'status' => 'Closed',
        'assigned_to' => $user->id,
        'reported_by' => $user->id,
        'expected_result' => 'Test reports',
        'attachment' => null,
    ]);

    $this->actingAs($user);
    $response = $this->get(route('reports.comprehensive'));
    
    expect($response->status())->toBe(200);
});
