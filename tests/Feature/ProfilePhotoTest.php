<?php

use App\Models\AdminPermission;
use App\Models\Dorm;
use App\Models\User;
use App\Models\WorkerProfile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->dorm = Dorm::create(['name' => 'Dorm AA', 'code' => 'AA']);
});

function registration(array $overrides = []): array
{
    return array_merge([
        'name' => 'New User',
        'email' => 'new@example.com',
        'dorm_id' => test()->dorm->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $overrides);
}

it('requires a photo when a laundry worker registers', function () {
    post('/register', registration(['role' => 'worker']))
        ->assertSessionHasErrors(['avatar' => 'Laundry workers must upload a clear photo of their face.']);

    $this->assertGuest();
    expect(User::count())->toBe(0);
});

it('stores the photo of a registering worker', function () {
    post('/register', registration(['role' => 'worker', 'avatar' => fakePhoto()]))->assertRedirect();

    $worker = User::where('email', 'new@example.com')->first();
    expect($worker->hasAvatar())->toBeTrue()
        ->and($worker->avatar->mime)->toBe('image/png');
});

it('lets students register with or without a photo', function () {
    post('/register', registration(['role' => 'student']))->assertSessionHasNoErrors();
    expect(User::first()->hasAvatar())->toBeFalse();

    auth()->logout();
    post('/register', registration(['role' => 'student', 'email' => 'second@example.com', 'avatar' => fakePhoto()]))->assertSessionHasNoErrors();
    expect(User::where('email', 'second@example.com')->first()->hasAvatar())->toBeTrue();
});

it('rejects photos that are too small or not images', function () {
    post('/register', registration(['role' => 'worker', 'avatar' => fakePhoto('tiny-photo.png')]))
        ->assertSessionHasErrors(['avatar' => 'The photo must be at least 200 by 200 pixels.']);

    $notAnImage = \Illuminate\Http\UploadedFile::fake()->createWithContent('face.jpg', 'definitely not an image');
    post('/register', registration(['role' => 'worker', 'avatar' => $notAnImage]))->assertSessionHasErrors('avatar');
});

it('serves photos only to signed-in users', function () {
    $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
    $worker->setAvatar(fakePhoto());
    $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

    $this->get($worker->avatarUrl())->assertRedirect(route('login'));

    actingAs($student)->get($worker->avatarUrl())
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');

    actingAs($student)->get(route('avatars.show', $student))->assertNotFound();
});

it('lets a user change their photo from the profile page', function () {
    $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

    actingAs($student)->post(route('profile.avatar.update'), ['avatar' => fakePhoto()])
        ->assertRedirect(route('profile.edit'));
    expect($student->fresh()->hasAvatar())->toBeTrue();

    actingAs($student)->delete(route('profile.avatar.destroy'))->assertRedirect(route('profile.edit'));
    expect($student->fresh()->hasAvatar())->toBeFalse()
        ->and($student->avatar()->exists())->toBeFalse();
});

it('does not let a worker remove their photo', function () {
    $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
    $worker->setAvatar(fakePhoto());

    actingAs($worker)->delete(route('profile.avatar.destroy'))->assertForbidden();
    expect($worker->fresh()->hasAvatar())->toBeTrue();
});

it('will not approve a worker who has no photo', function () {
    $head = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $head->adminPermissions()->create(['permission' => AdminPermission::ADMINS]);
    $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
    WorkerProfile::create(['user_id' => $worker->id, 'is_approved' => false]);

    actingAs($head)->post(route('admin.workers.approve', $worker))->assertSessionHasErrors('approve');
    expect($worker->workerProfile->fresh()->is_approved)->toBeFalse();

    $worker->setAvatar(fakePhoto());
    actingAs($head)->post(route('admin.workers.approve', $worker))->assertSessionHasNoErrors();
    expect($worker->workerProfile->fresh()->is_approved)->toBeTrue();
});

it('does not load photo data when listing users', function () {
    $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
    $worker->setAvatar(fakePhoto());

    expect(User::find($worker->id)->getAttributes())->not->toHaveKey('data')
        ->and($worker->fresh()->toArray())->not->toHaveKey('avatar');
});
