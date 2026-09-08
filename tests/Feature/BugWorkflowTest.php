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

function createBugWorkflowFixture(array $bugOverrides = []): array
{
    $qa = User::factory()->create(['role' => 'QA Tester']);
    $developer = User::factory()->create(['role' => 'Developer']);
    $otherDeveloper = User::factory()->create(['role' => 'Developer']);

    $project = Project::create([
        'name' => 'Bug Workflow Project',
        'description' => 'Workflow test',
        'status' => 'Aktif',
        'test_plan' => 'Workflow',
    ]);

    $requirement = Requirement::create([
        'project_id' => $project->id,
        'code' => 'REQ-WF',
        'description' => 'Workflow requirement',
    ]);

    $suite = TestSuite::create([
        'project_id' => $project->id,
        'name' => 'Workflow Suite',
    ]);

    $testCase = TestCase::create([
        'test_suite_id' => $suite->id,
        'requirement_id' => $requirement->id,
        'title' => 'Workflow case',
        'steps' => 'Steps',
        'expected_result' => 'Expected',
        'priority' => 'High',
    ]);

    $testRun = TestRun::create([
        'project_id' => $project->id,
        'title' => 'Workflow run',
        'status' => 'Active',
    ]);

    $result = TestResult::create([
        'test_run_id' => $testRun->id,
        'test_case_id' => $testCase->id,
        'status' => 'Failed',
        'executed_by' => $qa->id,
    ]);

    $bug = Bug::create(array_merge([
        'test_result_id' => $result->id,
        'title' => 'Workflow bug',
        'description' => 'Workflow bug',
        'status' => Bug::STATUS_OPEN,
        'assigned_to' => $developer->id,
        'reported_by' => $qa->id,
        'expected_result' => 'Expected',
        'attachment' => null,
    ], $bugOverrides));

    return compact('qa', 'developer', 'otherDeveloper', 'bug', 'result', 'testRun');
}

test('developer can move own bug from open to in progress', function () {
    $fixture = createBugWorkflowFixture();

    $this->actingAs($fixture['developer'])
        ->patch(route('bugs.update-status', $fixture['bug']->id), [
            'status' => Bug::STATUS_IN_PROGRESS,
        ])
        ->assertRedirect();

    expect($fixture['bug']->refresh()->status)->toBe(Bug::STATUS_IN_PROGRESS);
    expect(BugHistory::where('bug_id', $fixture['bug']->id)->count())->toBe(1);
});

test('developer cannot change another developers bug', function () {
    $fixture = createBugWorkflowFixture();

    $this->actingAs($fixture['otherDeveloper'])
        ->patch(route('bugs.update-status', $fixture['bug']->id), [
            'status' => Bug::STATUS_IN_PROGRESS,
        ])
        ->assertForbidden();

    expect($fixture['bug']->refresh()->status)->toBe(Bug::STATUS_OPEN);
});

test('developer cannot skip directly to done in review', function () {
    $fixture = createBugWorkflowFixture();

    $this->actingAs($fixture['developer'])
        ->patch(route('bugs.update-status', $fixture['bug']->id), [
            'status' => Bug::STATUS_DONE_IN_REVIEW,
        ])
        ->assertForbidden();

    expect($fixture['bug']->refresh()->status)->toBe(Bug::STATUS_OPEN);
});

test('qa can resolve a bug only after done in review', function () {
    $fixture = createBugWorkflowFixture(['status' => Bug::STATUS_DONE_IN_REVIEW]);

    $this->actingAs($fixture['qa'])
        ->patch(route('bugs.update-status', $fixture['bug']->id), [
            'status' => Bug::STATUS_RESOLVED,
        ])
        ->assertRedirect();

    expect($fixture['bug']->refresh()->status)->toBe(Bug::STATUS_RESOLVED)
        ->and($fixture['result']->refresh()->status)->toBe('Passed');
});

test('qa can reopen a bug after failed retest', function () {
    $fixture = createBugWorkflowFixture(['status' => Bug::STATUS_DONE_IN_REVIEW]);

    $this->actingAs($fixture['qa'])
        ->patch(route('bugs.update-status', $fixture['bug']->id), [
            'status' => Bug::STATUS_REOPENED,
        ])
        ->assertRedirect();

    expect($fixture['bug']->refresh()->status)->toBe(Bug::STATUS_REOPENED)
        ->and($fixture['bug']->finish_date)->toBeNull()
        ->and($fixture['result']->refresh()->status)->toBe('Failed');
});
