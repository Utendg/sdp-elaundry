<?php

use App\Models\AdminPermission;
use App\Models\Complaint;
use App\Models\Dorm;
use App\Models\Order;
use App\Models\Rating;
use App\Models\User;
use App\Models\WorkerProfile;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);
    $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'dorm_id' => $this->dorm->id]);
    $this->otherStudent = User::factory()->create(['role' => User::ROLE_STUDENT, 'dorm_id' => $this->dorm->id]);
    $this->worker = User::factory()->create(['role' => User::ROLE_WORKER, 'dorm_id' => $this->dorm->id]);
    WorkerProfile::create(['user_id' => $this->worker->id, 'is_approved' => true, 'is_available' => true]);

    $this->order = Order::create([
        'student_id' => $this->student->id,
        'worker_id' => $this->worker->id,
        'dorm_id' => $this->dorm->id,
        'status' => 'completed',
        'total_price' => 400,
    ]);
});

// ----- Complaints -----

it('lets a student file a complaint about their own order and targets that order\'s worker', function () {
    actingAs($this->student)->post(route('student.complaints.store'), [
        'order_id' => $this->order->id,
        'type' => 'damaged',
        'subject' => 'Torn shirt',
        'description' => 'One shirt came back with a tear on the sleeve.',
    ])->assertRedirect();

    $complaint = Complaint::first();
    expect($complaint)->not->toBeNull()
        ->and($complaint->complainant_id)->toBe($this->student->id)
        ->and($complaint->against_id)->toBe($this->worker->id)
        ->and($complaint->status)->toBe('open');
});

it('validates the complaint type, subject and description', function () {
    actingAs($this->student)->post(route('student.complaints.store'), [
        'type' => 'not-a-type',
        'subject' => '',
        'description' => '',
    ])->assertSessionHasErrors(['type', 'subject', 'description']);

    expect(Complaint::count())->toBe(0);
});

it('does not let a student complain about another student\'s order', function () {
    actingAs($this->otherStudent)->post(route('student.complaints.store'), [
        'order_id' => $this->order->id,
        'type' => 'missing',
        'subject' => 'Missing sock',
        'description' => 'A sock is missing.',
    ])->assertSessionHasErrors('order_id');

    expect(Complaint::count())->toBe(0);
});

it('shows a student only their own complaints', function () {
    Complaint::create(['complainant_id' => $this->student->id, 'type' => 'other', 'subject' => 'Mine', 'description' => 'x', 'status' => 'open']);
    Complaint::create(['complainant_id' => $this->otherStudent->id, 'type' => 'other', 'subject' => 'Someone else', 'description' => 'y', 'status' => 'open']);

    actingAs($this->student)->get(route('student.complaints.index'))
        ->assertOk()
        ->assertSee('Mine')
        ->assertDontSee('Someone else');
});

it('lets only admins with the complaints responsibility handle complaints', function () {
    $complaint = Complaint::create(['complainant_id' => $this->student->id, 'type' => 'delayed', 'subject' => 'Late', 'description' => 'z', 'status' => 'open']);

    $ordersOnly = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $ordersOnly->adminPermissions()->create(['permission' => AdminPermission::ORDERS]);
    actingAs($ordersOnly)->get(route('admin.complaints.index'))->assertForbidden();
    actingAs($ordersOnly)->patch(route('admin.complaints.update', $complaint), ['status' => 'resolved'])->assertForbidden();

    $officer = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $officer->adminPermissions()->create(['permission' => AdminPermission::COMPLAINTS]);
    actingAs($officer)->patch(route('admin.complaints.update', $complaint), ['status' => 'under_review'])->assertRedirect();

    expect($complaint->fresh()->status)->toBe('under_review');
});

it('keeps workers out of the student complaint area', function () {
    actingAs($this->worker)->get(route('student.complaints.index'))->assertForbidden();
});

// ----- Ratings -----

it('allows only one student rating per order', function () {
    actingAs($this->student)->post(route('student.orders.rate', $this->order), ['stars' => 4])->assertRedirect();
    actingAs($this->student)->post(route('student.orders.rate', $this->order), ['stars' => 1])->assertSessionHasErrors('rating');

    expect(Rating::where('order_id', $this->order->id)->where('direction', Rating::STUDENT_TO_WORKER)->count())->toBe(1)
        ->and((float) $this->worker->workerProfile->fresh()->rating_avg)->toBe(4.0);
});

it('rejects star values outside 1 to 5', function () {
    actingAs($this->student)->post(route('student.orders.rate', $this->order), ['stars' => 6])->assertSessionHasErrors('stars');
    actingAs($this->student)->post(route('student.orders.rate', $this->order), ['stars' => 0])->assertSessionHasErrors('stars');

    expect(Rating::count())->toBe(0);
});

it('does not let a student rate someone else\'s order', function () {
    actingAs($this->otherStudent)->post(route('student.orders.rate', $this->order), ['stars' => 5])->assertForbidden();
});
