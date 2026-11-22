<?php

use App\Models\AdminPermission;
use App\Models\Dorm;
use App\Models\ServiceItem;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->app['env'] = 'production';
    putenv('SEED_DEMO_DATA');
});

afterEach(function () {
    putenv('HEAD_ADMIN_EMAIL');
    putenv('HEAD_ADMIN_PASSWORD');
    $this->app['env'] = 'testing';
});

it('creates reference data and the head of residence life, but no demo accounts, in production', function () {
    putenv('HEAD_ADMIN_EMAIL=reslife@aun.edu.ng');
    putenv('HEAD_ADMIN_PASSWORD=a-strong-first-password');

    app(DatabaseSeeder::class)->setContainer(app())->__invoke();

    expect(Dorm::count())->toBe(9)
        ->and(ServiceItem::count())->toBeGreaterThan(0)
        ->and(User::count())->toBe(1)
        ->and(User::where('email', 'admin@aun.edu.ng')->exists())->toBeFalse();

    $head = User::where('email', 'reslife@aun.edu.ng')->first();
    expect($head->isAdmin())->toBeTrue()
        ->and($head->isHeadAdmin())->toBeTrue()
        ->and($head->adminPermissions()->where('permission', AdminPermission::ADMINS)->exists())->toBeTrue();
});

it('does not overwrite prices or the head admin password when seeding again', function () {
    putenv('HEAD_ADMIN_EMAIL=reslife@aun.edu.ng');
    putenv('HEAD_ADMIN_PASSWORD=a-strong-first-password');
    app(DatabaseSeeder::class)->setContainer(app())->__invoke();

    $item = ServiceItem::first();
    $item->update(['unit_price' => 999]);
    $head = User::where('email', 'reslife@aun.edu.ng')->first();
    $head->update(['password' => 'changed-by-the-head']);

    app(DatabaseSeeder::class)->setContainer(app())->__invoke();

    expect((float) $item->fresh()->unit_price)->toBe(999.0)
        ->and(Hash::check('changed-by-the-head', $head->fresh()->password))->toBeTrue()
        ->and(User::count())->toBe(1);
});

it('creates no administrator when the head admin variables are missing', function () {
    app(DatabaseSeeder::class)->setContainer(app())->__invoke();

    expect(User::count())->toBe(0);
});
