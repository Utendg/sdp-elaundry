<?php

namespace Database\Seeders;

use App\Models\Dorm;
use Illuminate\Database\Seeder;

class DormSeeder extends Seeder
{
    /**
     * The nine AUN residence halls covered by the laundry service.
     */
    public const DORMS = [
        ['name' => 'Dorm AA', 'code' => 'AA'],
        ['name' => 'Dorm BB', 'code' => 'BB'],
        ['name' => 'Dorm CC', 'code' => 'CC'],
        ['name' => 'Dorm DD', 'code' => 'DD'],
        ['name' => 'Dorm EE', 'code' => 'EE'],
        ['name' => 'Dorm FF', 'code' => 'FF'],
        ['name' => 'Volpi Boys', 'code' => 'VB'],
        ['name' => 'Aisha Kande', 'code' => 'AK'],
        ['name' => 'Volpi Girls', 'code' => 'VG'],
    ];

    public function run(): void
    {
        // Only fill an empty table, so halls edited by administrators are not overwritten on redeploy.
        if (Dorm::exists()) {
            return;
        }

        foreach (self::DORMS as $dorm) {
            Dorm::updateOrCreate(['code' => $dorm['code']], $dorm);
        }
    }
}
