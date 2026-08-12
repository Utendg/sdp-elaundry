<?php

use App\Models\Dorm;
use App\Models\Order;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Notifications\OrderStatusChanged;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);
    $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'dorm_id' => $this->dorm->id]);
    $this->worker = User::factory()->create(['role' => User::ROLE_WORKER, 'dorm_id' => $this->dorm->id]);
    WorkerProfile::create(['user_id' => $this->worker->id, 'is_approved' => true]);
});

it('shows the notifications index', function () {
    actingAs($this->student)->get(route('notifications.index'))->assertOk();
});

it('marks a notification read and redirects to its url', function () {
    $order = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'dorm_id' => $this->dorm->id,
        'status' => 'accepted',
        'total_price' => 100,
    ]);
    $this->student->notify(new OrderStatusChanged($order, 'accepted'));
    $note = $this->student->notifications()->first();

    actingAs($this->student)
        ->get(route('notifications.read', $note->id))
        ->assertRedirect(route('student.orders.show', $order));

    expect($note->fresh()->read_at)->not->toBeNull();
});

it('marks all notifications read', function () {
    $order = Order::create([
        'student_id' => $this->student->id, 'worker_id' => $this->worker->id,
        'dorm_id' => $this->dorm->id, 'status' => 'accepted', 'total_price' => 100,
    ]);
    $this->student->notify(new OrderStatusChanged($order, 'accepted'));
    $this->student->notify(new OrderStatusChanged($order, 'washing'));

    expect($this->student->unreadNotifications()->count())->toBe(2);

    actingAs($this->student)->post(route('notifications.readAll'))->assertRedirect();

    expect($this->student->fresh()->unreadNotifications()->count())->toBe(0);
});

it("forbids reading another user's notification", function () {
    $order = Order::create([
        'student_id' => $this->student->id, 'worker_id' => $this->worker->id,
        'dorm_id' => $this->dorm->id, 'status' => 'accepted', 'total_price' => 100,
    ]);
    $this->student->notify(new OrderStatusChanged($order, 'accepted'));
    $note = $this->student->notifications()->first();

    actingAs($this->worker)
        ->get(route('notifications.read', $note->id))
        ->assertNotFound();
});

it('renders the public landing page', function () {
    $this->get('/')->assertOk()->assertSee('E-Laundry');
});
