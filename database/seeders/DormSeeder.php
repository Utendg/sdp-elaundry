<?php

namespace Database\Seeders;

use App\Models\Dorm;
use Illuminate\Database\Seeder;

class DormSeeder extends Seeder
{
    /**
     * The nine AUN residence halls covered by the laundry service.
     */
    public function run(): void
    {
        $dorms = [
            ['name' => 'New Dorm A', 'code' => 'NDA'],
            ['name' => 'New Dorm B', 'code' => 'NDB'],
            ['name' => 'New Dorm C', 'code' => 'NDC'],
            ['name' => 'New Dorm D', 'code' => 'NDD'],
            ['name' => 'Old Dorm 1', 'code' => 'OD1'],
            ['name' => 'Old Dorm 2', 'code' => 'OD2'],
            ['name' => 'Old Dorm 3', 'code' => 'OD3'],
            ['name' => 'Ladies Village', 'code' => 'LV'],
            ['name' => 'Gender Village', 'code' => 'GV'],
        ];

        foreach ($dorms as $dorm) {
            Dorm::updateOrCreate(['code' => $dorm['code']], $dorm);
        }
    }
}
