<?php

use App\Models\User;

test('the application redirects unauthenticated users to login', function () {
    $response = $this->get('/');

    $response->assertStatus(302)->assertRedirectToRoute('login');
});

test('user can login with valid credentials', function () {
    // Create a test user
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'role' => 'QA Tester',
    ]);

    // POST to login endpoint with correct credentials
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    // Should redirect to dashboard
    $response->assertStatus(302);
    $response->assertRedirectToRoute('dashboard.qa');
    
    // User should be authenticated
    $this->assertAuthenticatedAs($user);
});
