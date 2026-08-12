<?php

use App\Models\Dorm;
use App\Models\Order;
use App\Models\ServiceItem;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Services\OrderService;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);

    $this->student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
        'dorm_id' => $this->dorm->id,
    ]);

    $this->worker = User::factory()->create([
        'role' => User::ROLE_WORKER,
        'dorm_id' => $this->dorm->id,
    ]);
    WorkerProfile::create([
        'user_id' => $this->worker->id,
        'is_approved' => true,
        'is_available' => true,
    ]);

    $this->shirt = ServiceItem::create(['name' => 'Shirt', 'service' => 'wash_iron', 'unit_price' => 200]);
    $this->trousers = ServiceItem::create(['name' => 'Trousers', 'service' => 'wash_iron', 'unit_price' => 250]);
});

it('shows approved workers to a student', function () {
    actingAs($this->student)
        ->get(route('student.workers.index'))
        ->assertOk()
        ->assertSee($this->worker->name);
});

it('hides unapproved workers', function () {
    $pending = User::factory()->create(['role' => User::ROLE_WORKER, 'dorm_id' => $this->dorm->id]);
    WorkerProfile::create(['user_id' => $pending->id, 'is_approved' => false]);

    actingAs($this->student)
        ->get(route('student.workers.index'))
        ->assertOk()
        ->assertDontSee($pending->name);
});

it('places an order and prices it from the official list', function () {
    actingAs($this->student)
        ->post(route('student.orders.store'), [
            'worker_id' => $this->worker->id,
            'items' => [
                ['service_item_id' => $this->shirt->id, 'quantity' => 3],   // 600
                ['service_item_id' => $this->trousers->id, 'quantity' => 0], // dropped
                ['service_item_id' => $this->trousers->id, 'quantity' => 2], // 500
            ],
        ])
        ->assertRedirect();

    $order = Order::first();
    expect($order)->not->toBeNull()
        ->and((float) $order->total_price)->toBe(1100.0)
        ->and($order->status)->toBe('pending')
        ->and($order->items)->toHaveCount(2)
        ->and($order->reference)->toStartWith('ELD-');
});

it('ignores client-supplied prices and uses the server price', function () {
    actingAs($this->student)
        ->post(route('student.orders.store'), [
            'worker_id' => $this->worker->id,
            'items' => [
                ['service_item_id' => $this->shirt->id, 'quantity' => 1, 'unit_price' => 1],
            ],
        ])->assertRedirect();

    expect((float) Order::first()->total_price)->toBe(200.0);
});

it('lets a student cancel a pending order', function () {
    $order = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'dorm_id' => $this->dorm->id,
        'status' => 'pending',
        'total_price' => 200,
    ]);

    actingAs($this->student)
        ->post(route('student.orders.cancel', $order), ['cancel_reason' => 'Changed my mind'])
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('cancelled');
});

it('prevents cancelling an order already in progress', function () {
    $order = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'status' => 'washing',
        'total_price' => 200,
    ]);

    actingAs($this->student)
        ->post(route('student.orders.cancel', $order))
        ->assertSessionHasErrors('status');

    expect($order->fresh()->status)->toBe('washing');
});

it('lets a student rate a completed order and updates the worker average', function () {
    $order = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'status' => 'completed',
        'total_price' => 200,
    ]);

    actingAs($this->student)
        ->post(route('student.orders.rate', $order), ['stars' => 5, 'comment' => 'Great!'])
        ->assertRedirect();

    expect((float) $this->worker->workerProfile->fresh()->rating_avg)->toBe(5.0)
        ->and($this->worker->workerProfile->fresh()->ratings_count)->toBe(1);
});

it('blocks rating an order that is not completed', function () {
    $order = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'status' => 'pending',
        'total_price' => 200,
    ]);

    actingAs($this->student)
        ->post(route('student.orders.rate', $order), ['stars' => 5])
        ->assertSessionHasErrors('rating');
});

it("forbids viewing another student's order", function () {
    $other = User::factory()->create(['role' => User::ROLE_STUDENT, 'dorm_id' => $this->dorm->id]);
    $order = Order::create([
        'student_id' => $other->id,
        'worker_id' => $this->worker->id,
        'status' => 'pending',
        'total_price' => 200,
    ]);

    actingAs($this->student)
        ->get(route('student.orders.show', $order))
        ->assertForbidden();
});

it('blocks non-students from the student area', function () {
    actingAs($this->worker)
        ->get(route('student.workers.index'))
        ->assertForbidden();
});

it('records a status transition in history via the service', function () {
    $order = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'status' => 'pending',
        'total_price' => 200,
    ]);

    app(OrderService::class)->transition($order, 'accepted', $this->worker, 'Accepted');

    expect($order->fresh()->status)->toBe('accepted')
        ->and($order->accepted_at)->not->toBeNull()
        ->and($order->statusHistory()->where('status', 'accepted')->exists())->toBeTrue();
});
