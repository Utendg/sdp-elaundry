<?php

use App\Models\AdminPermission;
use App\Models\Complaint;
use App\Models\Dorm;
use App\Models\Order;
use App\Models\ServiceItem;
use App\Models\User;
use App\Models\WorkerProfile;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->dorm = Dorm::create(['name' => 'Test Hall', 'code' => 'TH']);
    // The Head of Residence Life holds every responsibility.
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->admin->adminPermissions()->create(['permission' => AdminPermission::ADMINS]);
    $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'dorm_id' => $this->dorm->id]);
    $this->worker = User::factory()->create(['role' => User::ROLE_WORKER, 'dorm_id' => $this->dorm->id]);
    $this->profile = WorkerProfile::create(['user_id' => $this->worker->id, 'is_approved' => false]);
});

it('blocks non-admins from the admin area', function () {
    actingAs($this->student)->get(route('admin.workers.index'))->assertForbidden();
    actingAs($this->worker)->get(route('admin.orders.index'))->assertForbidden();
});

it('approves a worker and notifies them', function () {
    actingAs($this->admin)
        ->post(route('admin.workers.approve', $this->worker))
        ->assertRedirect();

    $profile = $this->profile->fresh();
    expect($profile->is_approved)->toBeTrue()
        ->and($profile->approved_by)->toBe($this->admin->id)
        ->and($profile->approved_at)->not->toBeNull()
        ->and($this->worker->fresh()->notifications)->toHaveCount(1);
});

it('revokes a worker approval', function () {
    $this->profile->update(['is_approved' => true, 'approved_at' => now(), 'approved_by' => $this->admin->id]);

    actingAs($this->admin)
        ->post(route('admin.workers.revoke', $this->worker))
        ->assertRedirect();

    expect($this->profile->fresh()->is_approved)->toBeFalse();
});

it('adds, updates, toggles and deletes a price-list item', function () {
    actingAs($this->admin)
        ->post(route('admin.service-items.store'), ['name' => 'Cap', 'service' => 'wash', 'unit_price' => 120])
        ->assertRedirect();
    $item = ServiceItem::where('name', 'Cap')->first();
    expect($item)->not->toBeNull()->and((float) $item->unit_price)->toBe(120.0);

    actingAs($this->admin)
        ->patch(route('admin.service-items.update', $item), ['name' => 'Cap', 'service' => 'wash', 'unit_price' => 150])
        ->assertRedirect();
    expect((float) $item->fresh()->unit_price)->toBe(150.0);

    actingAs($this->admin)->post(route('admin.service-items.toggle', $item))->assertRedirect();
    expect($item->fresh()->is_active)->toBeFalse();

    actingAs($this->admin)->delete(route('admin.service-items.destroy', $item))->assertRedirect();
    expect(ServiceItem::find($item->id))->toBeNull();
});

it('validates price-list input', function () {
    actingAs($this->admin)
        ->post(route('admin.service-items.store'), ['name' => '', 'service' => 'invalid', 'unit_price' => -5])
        ->assertSessionHasErrors(['name', 'service', 'unit_price']);
});

it('creates a dorm and enforces unique code', function () {
    actingAs($this->admin)
        ->post(route('admin.dorms.store'), ['name' => 'New Hall', 'code' => 'NH'])
        ->assertRedirect();
    expect(Dorm::where('code', 'NH')->exists())->toBeTrue();

    actingAs($this->admin)
        ->post(route('admin.dorms.store'), ['name' => 'Dup', 'code' => 'NH'])
        ->assertSessionHasErrors('code');
});

it('monitors and filters all orders', function () {
    Order::create(['student_id' => $this->student->id, 'worker_id' => $this->worker->id, 'dorm_id' => $this->dorm->id, 'status' => 'pending', 'total_price' => 100]);
    Order::create(['student_id' => $this->student->id, 'worker_id' => $this->worker->id, 'dorm_id' => $this->dorm->id, 'status' => 'completed', 'total_price' => 200]);

    actingAs($this->admin)
        ->get(route('admin.orders.index', ['status' => 'completed']))
        ->assertOk()
        ->assertViewHas('orders', fn ($orders) => $orders->total() === 1);
});

it('resolves a complaint and stamps the resolver', function () {
    $complaint = Complaint::create([
        'complainant_id' => $this->student->id,
        'type' => 'damaged',
        'subject' => 'Torn shirt',
        'description' => 'My shirt came back torn.',
        'status' => 'open',
    ]);

    actingAs($this->admin)
        ->patch(route('admin.complaints.update', $complaint), ['status' => 'resolved', 'resolution' => 'Refunded and warned worker.'])
        ->assertRedirect();

    $fresh = $complaint->fresh();
    expect($fresh->status)->toBe('resolved')
        ->and($fresh->resolved_by)->toBe($this->admin->id)
        ->and($fresh->resolved_at)->not->toBeNull()
        ->and($fresh->resolution)->toBe('Refunded and warned worker.');
});

it('renders all admin index pages', function () {
    foreach (['admin.workers.index', 'admin.service-items.index', 'admin.dorms.index', 'admin.orders.index', 'admin.complaints.index', 'admin.admins.index'] as $route) {
        actingAs($this->admin)->get(route($route))->assertOk();
    }
});

it('declines a pending worker with a reason and notifies them', function () {
    actingAs($this->admin)
        ->post(route('admin.workers.decline', $this->worker), ['reason' => 'No reference from the hall supervisor'])
        ->assertRedirect();

    $profile = $this->profile->fresh();
    expect($profile->isDeclined())->toBeTrue()
        ->and($profile->is_approved)->toBeFalse()
        ->and($profile->declined_by)->toBe($this->admin->id)
        ->and($profile->decline_reason)->toBe('No reference from the hall supervisor')
        ->and($this->worker->fresh()->notifications->first()->data['type'])->toBe('worker_declined');

    // The declined worker still cannot reach orders, and sees why on the dashboard.
    actingAs($this->worker)->get(route('worker.orders.index'))->assertRedirect(route('dashboard'));
    actingAs($this->worker)->get(route('dashboard'))->assertSee('application was declined');
});

it('does not decline a worker who is already approved', function () {
    $this->profile->update(['is_approved' => true]);

    actingAs($this->admin)
        ->post(route('admin.workers.decline', $this->worker))
        ->assertSessionHasErrors('decline');

    expect($this->profile->fresh()->declined_at)->toBeNull();
});

it('can approve a previously declined worker', function () {
    $this->profile->update(['declined_at' => now(), 'declined_by' => $this->admin->id, 'decline_reason' => 'x']);

    actingAs($this->admin)->post(route('admin.workers.approve', $this->worker))->assertRedirect();

    $profile = $this->profile->fresh();
    expect($profile->is_approved)->toBeTrue()->and($profile->declined_at)->toBeNull();
});

it('lets the head of residence life create an admin with chosen responsibilities', function () {
    actingAs($this->admin)->post(route('admin.admins.store'), [
        'name' => 'Hall Supervisor',
        'email' => 'supervisor@aun.edu.ng',
        'password' => 'password',
        'password_confirmation' => 'password',
        'permissions' => ['complaints', 'orders'],
    ])->assertRedirect();

    $new = User::where('email', 'supervisor@aun.edu.ng')->first();
    expect($new->isAdmin())->toBeTrue()
        ->and($new->adminPermissions->pluck('permission')->sort()->values()->all())->toBe(['complaints', 'orders'])
        ->and($new->adminPermissions->first()->granted_by)->toBe($this->admin->id);

    // Only the assigned areas open for the new admin.
    actingAs($new)->get(route('admin.complaints.index'))->assertOk();
    actingAs($new)->get(route('admin.orders.index'))->assertOk();
    actingAs($new)->get(route('admin.workers.index'))->assertForbidden();
    actingAs($new)->get(route('admin.service-items.index'))->assertForbidden();
    actingAs($new)->get(route('admin.admins.index'))->assertForbidden();
});

it('does not let the admins responsibility be granted', function () {
    actingAs($this->admin)->post(route('admin.admins.store'), [
        'name' => 'Sneaky',
        'email' => 'sneaky@aun.edu.ng',
        'password' => 'password',
        'password_confirmation' => 'password',
        'permissions' => ['admins'],
    ])->assertSessionHasErrors('permissions.0');

    expect(User::where('email', 'sneaky@aun.edu.ng')->exists())->toBeFalse();
});

it('updates responsibilities and deactivates an admin, who can then no longer sign in', function () {
    $other = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $other->adminPermissions()->create(['permission' => 'dorms']);

    actingAs($this->admin)->patch(route('admin.admins.update', $other), ['permissions' => ['prices']])->assertRedirect();
    expect($other->fresh()->adminPermissions->pluck('permission')->all())->toBe(['prices']);

    actingAs($this->admin)->post(route('admin.admins.toggle', $other))->assertRedirect();
    expect($other->fresh()->is_active)->toBeFalse();

    auth()->logout();
    $this->post('/login', ['email' => $other->email, 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('stops ordinary admins from managing admin accounts', function () {
    $other = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $other->adminPermissions()->create(['permission' => 'workers']);

    actingAs($other)->get(route('admin.admins.index'))->assertForbidden();
    actingAs($other)->post(route('admin.admins.toggle', $this->admin))->assertForbidden();
});

it('does not let the head of residence life change their own account here', function () {
    actingAs($this->admin)->post(route('admin.admins.toggle', $this->admin))->assertForbidden();
    expect($this->admin->fresh()->is_active)->toBeTrue();
});
