<?php

use App\Models\AdminPermission;
use App\Models\Dorm;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // halls in their usual order (ids ascending), deliberately not alphabetical
    $this->aa = Dorm::create(['name' => 'Dorm AA', 'code' => 'AA']);
    $this->vb = Dorm::create(['name' => 'Volpi Boys', 'code' => 'VB']);
    $this->ak = Dorm::create(['name' => 'Aisha Kande', 'code' => 'AK']);

    $this->head = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->head->adminPermissions()->create(['permission' => AdminPermission::ADMINS]);

    User::factory()->create(['role' => User::ROLE_STUDENT, 'name' => 'Zainab Volpi', 'dorm_id' => $this->vb->id]);
    User::factory()->create(['role' => User::ROLE_STUDENT, 'name' => 'Amina Kande', 'dorm_id' => $this->ak->id]);
    User::factory()->create(['role' => User::ROLE_STUDENT, 'name' => 'Musa AA', 'dorm_id' => $this->aa->id]);
    User::factory()->create(['role' => User::ROLE_STUDENT, 'name' => 'Bola AA', 'dorm_id' => $this->aa->id]);
    User::factory()->create(['role' => User::ROLE_WORKER, 'name' => 'Not A Student', 'dorm_id' => $this->aa->id]);
});

it('lists students grouped by residence hall in hall order', function () {
    $response = actingAs($this->head)->get(route('admin.students.index'))->assertOk();

    // halls appear in their usual order, and students are alphabetical within each hall
    $response->assertSeeInOrder(['Dorm AA', 'Bola AA', 'Musa AA', 'Volpi Boys', 'Zainab Volpi', 'Aisha Kande', 'Amina Kande']);
    $response->assertDontSee('Not A Student');
    $response->assertViewHas('total', 4);
});

it('filters students by hall and by search', function () {
    actingAs($this->head)->get(route('admin.students.index', ['dorm' => $this->vb->id]))
        ->assertOk()->assertSee('Zainab Volpi')->assertDontSee('Musa AA');

    actingAs($this->head)->get(route('admin.students.index', ['q' => 'bola']))
        ->assertOk()->assertSee('Bola AA')->assertDontSee('Zainab Volpi');
});

it('opens from the students figure on the admin dashboard', function () {
    actingAs($this->head)->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('admin.students.index'), false);
});

it('is only open to admins with the order monitoring responsibility', function () {
    $ordersAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $ordersAdmin->adminPermissions()->create(['permission' => AdminPermission::ORDERS]);
    actingAs($ordersAdmin)->get(route('admin.students.index'))->assertOk();

    $complaintsAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $complaintsAdmin->adminPermissions()->create(['permission' => AdminPermission::COMPLAINTS]);
    actingAs($complaintsAdmin)->get(route('admin.students.index'))->assertForbidden();

    $student = User::where('role', User::ROLE_STUDENT)->first();
    actingAs($student)->get(route('admin.students.index'))->assertForbidden();
});
