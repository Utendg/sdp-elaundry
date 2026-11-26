<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// The site now calls the people students hand clothes to "laundry agents" (they arrange the washing; their washers do it).
// The seeded profile bios on the live server were written as "Laundry worker for <hall>", so update them to match.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('worker_profiles')
            ->where('bio', 'like', 'Laundry worker for %')
            ->update(['bio' => DB::raw("REPLACE(bio, 'Laundry worker for', 'Laundry agent for')")]);
    }

    public function down(): void
    {
        DB::table('worker_profiles')
            ->where('bio', 'like', 'Laundry agent for %')
            ->update(['bio' => DB::raw("REPLACE(bio, 'Laundry agent for', 'Laundry worker for')")]);
    }
};
