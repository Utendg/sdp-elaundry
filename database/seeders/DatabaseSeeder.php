<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\Dorm;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with reference data and demo accounts.
     */
    public function run(): void
    {
        $this->call([
            DormSeeder::class,
            ServiceItemSeeder::class,
        ]);

        // On a live server only the reference data and the Head of Residence Life are created.
        // The demo accounts below (all with the password "password") exist only on development machines.
        if (app()->environment('production') && ! filter_var(getenv('SEED_DEMO_DATA'), FILTER_VALIDATE_BOOLEAN)) {
            $this->seedHeadOfResidenceLife();

            return;
        }

        $firstDorm = Dorm::where('code', 'AA')->first() ?? Dorm::orderBy('id')->first();

        // Head of Residence Life: holds every responsibility and manages the other admin accounts.
        $head = User::updateOrCreate(
            ['email' => 'admin@aun.edu.ng'],
            [
                'name' => 'Head of Residence Life',
                'role' => User::ROLE_ADMIN,
                'phone' => '+2340000000000',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );
        foreach ([...array_keys(AdminPermission::GRANTABLE), AdminPermission::ADMINS] as $permission) {
            $head->adminPermissions()->firstOrCreate(['permission' => $permission]);
        }

        // A second admin with limited responsibilities, created as the Head of Residence Life would.
        $officer = User::updateOrCreate(
            ['email' => 'complaints@aun.edu.ng'],
            [
                'name' => 'Complaints Officer',
                'role' => User::ROLE_ADMIN,
                'phone' => '+2340000000001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );
        foreach ([AdminPermission::ORDERS, AdminPermission::COMPLAINTS] as $permission) {
            $officer->adminPermissions()->firstOrCreate(['permission' => $permission], ['granted_by' => $head->id]);
        }

        // One approved laundry man per residence hall.
        // worker@aun.edu.ng is kept as the Dorm AA laundry man so the demo login stays the same.
        $laundryMen = [
            'AA' => ['worker@aun.edu.ng', 'Adamu Musa'],
            'BB' => ['laundry.bb@aun.edu.ng', 'Ibrahim Sani'],
            'CC' => ['laundry.cc@aun.edu.ng', 'Emeka Okafor'],
            'DD' => ['laundry.dd@aun.edu.ng', 'Musa Bello'],
            'EE' => ['laundry.ee@aun.edu.ng', 'Yakubu Danjuma'],
            'FF' => ['laundry.ff@aun.edu.ng', 'Chinedu Eze'],
            'VB' => ['laundry.vb@aun.edu.ng', 'Garba Usman'],
            'AK' => ['laundry.ak@aun.edu.ng', 'Tunde Adebayo'],
            'VG' => ['laundry.vg@aun.edu.ng', 'Sule Abdullahi'],
        ];

        $n = 0;
        foreach ($laundryMen as $code => [$email, $name]) {
            $dorm = Dorm::where('code', $code)->first();
            $n++;

            $worker = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'role' => User::ROLE_WORKER,
                    'phone' => sprintf('+23480300001%02d', $n),
                    'dorm_id' => $dorm?->id,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
            WorkerProfile::updateOrCreate(
                ['user_id' => $worker->id],
                [
                    'bio' => "Laundry man for {$dorm?->name}. Washing, drying and ironing with a quick turnaround.",
                    'is_approved' => true,
                    'is_available' => true,
                    'approved_at' => now(),
                ],
            );
        }

        // Pending worker (awaiting admin approval).
        $pending = User::updateOrCreate(
            ['email' => 'pending@aun.edu.ng'],
            [
                'name' => 'Bello (Pending Approval)',
                'role' => User::ROLE_WORKER,
                'phone' => '+2348030000002',
                'dorm_id' => $firstDorm?->id,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        WorkerProfile::updateOrCreate(
            ['user_id' => $pending->id],
            ['is_approved' => false],
        );

        // Demo student.
        User::updateOrCreate(
            ['email' => 'student@aun.edu.ng'],
            [
                'name' => 'David the Student',
                'role' => User::ROLE_STUDENT,
                'phone' => '+2348030000003',
                'dorm_id' => $firstDorm?->id,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }

    /**
     * Create the Head of Residence Life from HEAD_ADMIN_EMAIL / HEAD_ADMIN_PASSWORD (set on the host).
     * The account is only created once, so a password changed later on the Profile page is kept.
     */
    private function seedHeadOfResidenceLife(): void
    {
        $email = getenv('HEAD_ADMIN_EMAIL');
        $password = getenv('HEAD_ADMIN_PASSWORD');
        if (! $email || ! $password) {
            $this->command?->warn('HEAD_ADMIN_EMAIL or HEAD_ADMIN_PASSWORD is not set; no administrator was created.');

            return;
        }

        $head = User::firstOrCreate(
            ['email' => strtolower($email)],
            [
                'name' => getenv('HEAD_ADMIN_NAME') ?: 'Head of Residence Life',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );
        foreach ([...array_keys(AdminPermission::GRANTABLE), AdminPermission::ADMINS] as $permission) {
            $head->adminPermissions()->firstOrCreate(['permission' => $permission]);
        }
    }
}
