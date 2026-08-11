<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extra profile data for laundry workers, including the admin approval
     * gate and cached rating/throughput stats used to rank workers.
     */
    public function up(): void
    {
        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            // Workers cannot receive orders until an admin approves them.
            $table->boolean('is_approved')->default(false);
            // Workers can toggle whether they are currently accepting orders.
            $table->boolean('is_available')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            // Cached aggregates (recomputed when a rating/order is recorded).
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('ratings_count')->default(0);
            $table->unsignedInteger('orders_completed')->default(0);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_profiles');
    }
};
