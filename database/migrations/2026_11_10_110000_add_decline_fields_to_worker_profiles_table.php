<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets an administrator decline a laundry worker's application (with an optional reason)
 * instead of leaving it pending forever.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_profiles', function (Blueprint $table) {
            $table->timestamp('declined_at')->nullable()->after('approved_by');
            $table->foreignId('declined_by')->nullable()->after('declined_at')->constrained('users')->nullOnDelete();
            $table->string('decline_reason', 500)->nullable()->after('declined_by');
        });
    }

    public function down(): void
    {
        Schema::table('worker_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('declined_by');
            $table->dropColumn(['declined_at', 'decline_reason']);
        });
    }
};
