<?php

use App\Models\Bug;
use App\Models\BugHistory;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TestCase;
use App\Models\TestResult;
use App\Models\TestRun;
use App\Models\TestSuite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('bug status change creates history record', function () {
    $project = Project::create([
        'name' => 'Module 4 Project',
        'description' => 'Project untuk modul 4',
        'status' => 'Aktif',
        'test_plan' => 'Scope: history tracking',
    ]);

    $requirement = Requirement::create([
        'project_id' => $project->id,
        'code' => 'REQ-04',
        'description' => 'Fitur history tracking',
    ]);

    $suite = TestSuite::create([
        'project_id' => $project->id,
        'name' => 'History Suite',
    ]);

    $testCase = TestCase::create([
        'test_suite_id' => $suite->id,
        'requirement_id' => $requirement->id,
        'title' => 'Test history tracking',
        'steps' => '1. Create bug',
        'expected_result' => 'History should be recorded',
        'priority' => 'High',
    ]);

    $user = User::factory()->create(['role' => 'QA Tester']);
    $developer = User::factory()->create(['role' => 'Developer']);

    $testRun = TestRun::create([
        'project_id' => $project->id,
        'title' => 'History test run',
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
        'title' => 'History bug',
        'description' => 'Test bug for history tracking',
        'status' => 'Open',
        'assigned_to' => $developer->id,
        'reported_by' => $user->id,
        'expected_result' => 'Should record history',
        'attachment' => null,
    ]);

    // Authenticate as developer
    $this->actingAs($developer);

    // Change bug status
    $this->patch(route('bugs.update-status', $bug->id), [
        'status' => 'In Progress',
    ]);

    // Verify history was recorded
    $history = BugHistory::where('bug_id', $bug->id)->first();

    expect($history)->not->toBeNull()
        ->and($history->field_name)->toBe('status')
        ->and($history->old_value)->toBe('Open')
        ->and($history->new_value)->toBe('In Progress')
        ->and($history->changed_by)->toBe($developer->id);
});

test('bug history page displays timeline with filters', function () {
    $project = Project::create([
        'name' => 'Filter Project',
        'description' => 'Project untuk filter test',
        'status' => 'Aktif',
        'test_plan' => 'Filter test',
    ]);

    $user = User::factory()->create(['role' => 'QA Tester']);
    $developer = User::factory()->create(['role' => 'Developer']);

    // Create test data
    $requirement = Requirement::create([
        'project_id' => $project->id,
        'code' => 'REQ-05',
        'description' => 'Filter requirement',
    ]);

    $suite = TestSuite::create([
        'project_id' => $project->id,
        'name' => 'Filter Suite',
    ]);

    $testCase = TestCase::create([
        'test_suite_id' => $suite->id,
        'requirement_id' => $requirement->id,
        'title' => 'Filter test case',
        'steps' => 'Steps',
        'expected_result' => 'Expected result',
        'priority' => 'High',
    ]);

    $testRun = TestRun::create([
        'project_id' => $project->id,
        'title' => 'Filter test run',
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
        'title' => 'Filter bug',
        'description' => 'Bug for filter test',
        'status' => 'Open',
        'assigned_to' => $developer->id,
        'reported_by' => $user->id,
        'expected_result' => 'Test filter',
        'attachment' => null,
    ]);

    // Create history record
    BugHistory::create([
        'bug_id' => $bug->id,
        'changed_by' => $user->id,
        'field_name' => 'status',
        'old_value' => 'Open',
        'new_value' => 'In Progress',
        'description' => 'Status changed from Open to In Progress',
    ]);

    $this->actingAs($user);

    // Test page access
    $response = $this->get(route('report.bug-history'));
    expect($response->status())->toBe(200);

    // Test filter by project
    $response = $this->get(route('report.bug-history', ['project_id' => $project->id]));
    expect($response->status())->toBe(200);

    // Test filter by status
    $response = $this->get(route('report.bug-history', ['bug_status' => 'Open']));
    expect($response->status())->toBe(200);

    // Test bug detail page
    $response = $this->get(route('report.bug-detail', $bug->id));
    expect($response->status())->toBe(200);
});
