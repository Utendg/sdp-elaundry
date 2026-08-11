<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two-way ratings tied to a completed order: students rate workers and
     * workers rate students. One rating per direction per order.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rater_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ratee_id')->constrained('users')->cascadeOnDelete();
            $table->enum('direction', ['student_to_worker', 'worker_to_student']);
            $table->unsignedTinyInteger('stars'); // 1..5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
