<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Replace the placeholder dorm names with AUN's real residence halls.
 * Existing rows are renamed in place so students, workers and orders keep their dorm.
 */
return new class extends Migration
{
    private const MAP = [
        'NDA' => ['Dorm AA', 'AA'],
        'NDB' => ['Dorm BB', 'BB'],
        'NDC' => ['Dorm CC', 'CC'],
        'NDD' => ['Dorm DD', 'DD'],
        'OD1' => ['Dorm EE', 'EE'],
        'OD2' => ['Dorm FF', 'FF'],
        'OD3' => ['Volpi Boys', 'VB'],
        'LV' => ['Aisha Kande', 'AK'],
        'GV' => ['Volpi Girls', 'VG'],
    ];

    public function up(): void
    {
        foreach (self::MAP as $old => [$name, $code]) {
            DB::table('dorms')->where('code', $old)->update(['name' => $name, 'code' => $code, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        $oldNames = [
            'NDA' => 'New Dorm A', 'NDB' => 'New Dorm B', 'NDC' => 'New Dorm C', 'NDD' => 'New Dorm D',
            'OD1' => 'Old Dorm 1', 'OD2' => 'Old Dorm 2', 'OD3' => 'Old Dorm 3', 'LV' => 'Ladies Village', 'GV' => 'Gender Village',
        ];
        foreach (self::MAP as $old => [, $code]) {
            DB::table('dorms')->where('code', $code)->update(['name' => $oldNames[$old], 'code' => $old, 'updated_at' => now()]);
        }
    }
};
