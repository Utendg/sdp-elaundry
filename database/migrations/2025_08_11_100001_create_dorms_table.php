<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The nine AUN residence halls the laundry service covers.
     */
    public function up(): void
    {
        Schema::create('dorms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Now that dorms exists, wire up the users.dorm_id foreign key.
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('dorm_id')->references('id')->on('dorms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dorm_id']);
        });

        Schema::dropIfExists('dorms');
    }
};
