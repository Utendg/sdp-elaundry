<?php

namespace Database\Seeders;

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

        $firstDorm = Dorm::orderBy('id')->first();

        // Administrator (oversees the whole platform).
        User::updateOrCreate(
            ['email' => 'admin@aun.edu.ng'],
            [
                'name' => 'System Administrator',
                'role' => User::ROLE_ADMIN,
                'phone' => '+2340000000000',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        // Approved worker (can take orders).
        $worker = User::updateOrCreate(
            ['email' => 'worker@aun.edu.ng'],
            [
                'name' => 'Aisha the Launderer',
                'role' => User::ROLE_WORKER,
                'phone' => '+2348030000001',
                'dorm_id' => $firstDorm?->id,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        WorkerProfile::updateOrCreate(
            ['user_id' => $worker->id],
            [
                'bio' => 'Reliable, quick turnaround. Serving New Dorm A.',
                'is_approved' => true,
                'is_available' => true,
                'approved_at' => now(),
            ],
        );

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
}
