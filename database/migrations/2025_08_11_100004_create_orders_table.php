<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A laundry order placed by a student against a specific worker.
     * The status column drives the real-time tracking timeline.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique(); // human-friendly code e.g. ELD-8FK3Q2
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('dorm_id')->nullable()->index();

            $table->enum('status', [
                'pending',    // placed, awaiting worker acceptance
                'accepted',   // worker agreed to the job
                'picked_up',  // clothes collected from student
                'washing',
                'ironing',
                'ready',      // done, awaiting return/pickup
                'completed',  // returned to student
                'cancelled',  // cancelled by student
                'rejected',   // declined by worker
            ])->default('pending')->index();

            $table->decimal('total_price', 10, 2)->default(0);
            $table->text('notes')->nullable();              // special instructions
            $table->string('pickup_location')->nullable();
            $table->timestamp('scheduled_pickup_at')->nullable();

            // Lifecycle timestamps for auditing / dispute resolution.
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();

            $table->timestamps();

            $table->foreign('dorm_id')->references('id')->on('dorms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
