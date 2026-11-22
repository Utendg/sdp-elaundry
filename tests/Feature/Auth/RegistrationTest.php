<?php

use App\Models\Dorm;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new students can register', function () {
    $dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);

    $response = $this->post('/register', [
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'role' => 'student',
        'dorm_id' => $dorm->id,
        'phone' => '08000000000',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    expect(User::where('email', 'student@example.com')->first()->role)->toBe('student');
});

test('workers register with an unapproved profile', function () {
    $dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);

    $this->post('/register', [
        'name' => 'Test Worker',
        'email' => 'worker@example.com',
        'role' => 'worker',
        'dorm_id' => $dorm->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $worker = User::where('email', 'worker@example.com')->first();

    expect($worker->role)->toBe('worker')
        ->and($worker->workerProfile)->not->toBeNull()
        ->and($worker->workerProfile->is_approved)->toBeFalse();
});

test('registration requires a role and dorm', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['role', 'dorm_id']);
    $this->assertGuest();
});

test('a new worker registration notifies the admins responsible for workers', function () {
    $dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);
    $admins = User::factory()->count(2)->create(['role' => User::ROLE_ADMIN]);
    $admins[0]->adminPermissions()->create(['permission' => 'admins']);  // Head of Residence Life
    $admins[1]->adminPermissions()->create(['permission' => 'workers']);
    $complaintsOnly = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $complaintsOnly->adminPermissions()->create(['permission' => 'complaints']);

    $this->post('/register', [
        'name' => 'New Laundry Man',
        'email' => 'newworker@example.com',
        'role' => 'worker',
        'dorm_id' => $dorm->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    foreach ($admins as $admin) {
        expect($admin->fresh()->notifications)->toHaveCount(1)
            ->and($admin->fresh()->notifications->first()->data['type'])->toBe('worker_registered');
    }
    expect($complaintsOnly->fresh()->notifications)->toHaveCount(0);
});

test('a new student registration does not notify admins', function () {
    $dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->post('/register', [
        'name' => 'New Student',
        'email' => 'newstudent@example.com',
        'role' => 'student',
        'dorm_id' => $dorm->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect($admin->fresh()->notifications)->toHaveCount(0);
});
