<?php

use App\Models\Dorm;
use App\Models\Order;
use App\Models\Rating;
use App\Models\User;
use App\Models\WorkerProfile;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);

    $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'dorm_id' => $this->dorm->id]);

    $this->worker = User::factory()->create(['role' => User::ROLE_WORKER, 'dorm_id' => $this->dorm->id]);
    $this->profile = WorkerProfile::create(['user_id' => $this->worker->id, 'is_approved' => true, 'is_available' => true]);

    $this->makeOrder = fn (string $status = 'pending') => Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'dorm_id' => $this->dorm->id,
        'status' => $status,
        'total_price' => 400,
    ]);
});

it('lists the worker orders grouped by stage', function () {
    ($this->makeOrder)('pending');
    ($this->makeOrder)('washing');

    actingAs($this->worker)
        ->get(route('worker.orders.index'))
        ->assertOk();
});

it('lets a worker accept a pending order and notifies the student', function () {
    $order = ($this->makeOrder)('pending');

    actingAs($this->worker)
        ->post(route('worker.orders.accept', $order))
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('accepted')
        ->and($order->fresh()->accepted_at)->not->toBeNull()
        ->and($this->student->fresh()->notifications)->toHaveCount(1);
});

it('lets a worker reject a pending order', function () {
    $order = ($this->makeOrder)('pending');

    actingAs($this->worker)
        ->post(route('worker.orders.reject', $order), ['reason' => 'Too busy'])
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('rejected')
        ->and($order->fresh()->cancel_reason)->toBe('Too busy');
});

it('advances an order through valid pipeline steps', function () {
    $order = ($this->makeOrder)('accepted');

    actingAs($this->worker)->post(route('worker.orders.advance', $order), ['status' => 'picked_up'])->assertRedirect();
    expect($order->fresh()->status)->toBe('picked_up');

    actingAs($this->worker)->post(route('worker.orders.advance', $order), ['status' => 'washing'])->assertRedirect();
    expect($order->fresh()->status)->toBe('washing');
});

it('rejects an invalid status jump', function () {
    $order = ($this->makeOrder)('accepted');

    actingAs($this->worker)
        ->post(route('worker.orders.advance', $order), ['status' => 'completed'])
        ->assertSessionHasErrors('status');

    expect($order->fresh()->status)->toBe('accepted');
});

it('increments completed count when an order is marked completed', function () {
    $order = ($this->makeOrder)('ready');

    actingAs($this->worker)
        ->post(route('worker.orders.advance', $order), ['status' => 'completed'])
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('completed')
        ->and($this->profile->fresh()->orders_completed)->toBe(1);
});

it('lets a worker rate a student after completion', function () {
    $order = ($this->makeOrder)('completed');

    actingAs($this->worker)
        ->post(route('worker.orders.rate', $order), ['stars' => 4, 'comment' => 'Polite'])
        ->assertRedirect();

    expect(Rating::where('order_id', $order->id)->where('direction', Rating::WORKER_TO_STUDENT)->count())->toBe(1);
});

it("forbids acting on another worker's order", function () {
    $other = User::factory()->create(['role' => User::ROLE_WORKER, 'dorm_id' => $this->dorm->id]);
    WorkerProfile::create(['user_id' => $other->id, 'is_approved' => true]);
    $order = ($this->makeOrder)('pending');

    actingAs($other)
        ->post(route('worker.orders.accept', $order))
        ->assertForbidden();
});

it('blocks unapproved workers from order management', function () {
    $this->profile->update(['is_approved' => false]);

    actingAs($this->worker)
        ->get(route('worker.orders.index'))
        ->assertRedirect(route('dashboard'));
});

it('allows an unapproved worker to reach their profile', function () {
    $this->profile->update(['is_approved' => false]);

    actingAs($this->worker)
        ->get(route('worker.profile.edit'))
        ->assertOk();
});

it('lets a worker toggle availability', function () {
    actingAs($this->worker)
        ->post(route('worker.profile.availability'))
        ->assertRedirect();

    expect($this->profile->fresh()->is_available)->toBeFalse();
});

it('lets a worker update bio and phone', function () {
    actingAs($this->worker)
        ->patch(route('worker.profile.update'), ['bio' => 'Fast and reliable', 'phone' => '08012345678'])
        ->assertRedirect();

    expect($this->profile->fresh()->bio)->toBe('Fast and reliable')
        ->and($this->worker->fresh()->phone)->toBe('08012345678');
});

it('blocks students from the worker area', function () {
    actingAs($this->student)
        ->get(route('worker.orders.index'))
        ->assertForbidden();
});
