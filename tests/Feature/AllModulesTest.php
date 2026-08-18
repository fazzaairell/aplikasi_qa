<?php

use App\Models\User;
use App\Models\Project;

test('project has test plan field', function () {
    $project = Project::create([
        'name' => 'Test Project',
        'status' => 'Aktif',
        'test_plan' => 'Test plan content'
    ]);
    expect($project->test_plan)->toBe('Test plan content');
});

test('admin can view project with test plan', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'Admin'
    ]);

    $project = Project::create([
        'name' => 'Test Project',
        'status' => 'Aktif',
        'test_plan' => 'Test plan content'
    ]);

    $response = $this->actingAs($admin)->get(route('projects.show', $project->id));
    $response->assertStatus(200);
});

test('qa can access notifications timeline', function () {
    $qa = User::create([
        'name' => 'QA',
        'email' => 'qa@test.com',
        'password' => bcrypt('password'),
        'role' => 'QA Tester'
    ]);

    $response = $this->actingAs($qa)->get(route('notifications.timeline'));
    $response->assertStatus(200);
});

test('admin can access comprehensive reports', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'Admin'
    ]);

    $response = $this->actingAs($admin)->get(route('reports.comprehensive'));
    $response->assertStatus(200);
});
