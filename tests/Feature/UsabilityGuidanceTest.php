<?php

// Changes made after the user acceptance test: workers, students and Residence Life
// staff each had trouble with one page, so those pages now explain the next step.

use App\Models\AdminPermission;
use App\Models\Dorm;
use App\Models\Order;
use App\Models\User;
use App\Models\WorkerProfile;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);
    $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'dorm_id' => $this->dorm->id]);
    $this->worker = User::factory()->create(['role' => User::ROLE_WORKER, 'dorm_id' => $this->dorm->id]);
    WorkerProfile::create(['user_id' => $this->worker->id, 'is_approved' => true, 'is_available' => true]);

    $this->makeOrder = fn (string $status) => Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'dorm_id' => $this->dorm->id,
        'status' => $status,
        'total_price' => 400,
    ]);
});

it('tells the laundry worker what to do next at each stage', function () {
    actingAs($this->worker)
        ->get(route('worker.orders.show', ($this->makeOrder)('accepted')))
        ->assertOk()
        ->assertSee('Next step:')
        ->assertSee('Collect the clothes from the student');

    actingAs($this->worker)
        ->get(route('worker.orders.show', ($this->makeOrder)('washing')))
        ->assertOk()
        ->assertSee('For a wash-only order, press', false);
});

it('tells the student what happens next on the tracking page', function () {
    actingAs($this->student)
        ->get(route('student.orders.show', ($this->makeOrder)('pending')))
        ->assertOk()
        ->assertSee('What happens next:')
        ->assertSee('Waiting for your laundry agent to accept the order');

    actingAs($this->student)
        ->get(route('student.orders.show', ($this->makeOrder)('ready')))
        ->assertOk()
        ->assertSee('Your clothes are ready');
});

it('lets staff open one residence hall\'s orders with a single link', function () {
    $other = Dorm::create(['name' => 'Other Hall', 'code' => 'OH']);
    $mine = ($this->makeOrder)('pending');
    $elsewhere = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'dorm_id' => $other->id,
        'status' => 'pending',
        'total_price' => 200,
    ]);

    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $admin->adminPermissions()->create(['permission' => AdminPermission::ORDERS]);

    actingAs($admin)
        ->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee('All halls')
        ->assertSee(route('admin.orders.index', ['dorm_id' => $other->id]), false);

    actingAs($admin)
        ->get(route('admin.orders.index', ['dorm_id' => $other->id]))
        ->assertOk()
        ->assertSee($elsewhere->reference)
        ->assertDontSee($mine->reference);
});
