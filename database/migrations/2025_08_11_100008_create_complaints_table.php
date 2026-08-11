<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Misconduct / damaged / missing-item reports raised by a user and
     * triaged by admins. May optionally reference an order and a subject user.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('complainant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('against_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['damaged', 'missing', 'delayed', 'pricing', 'conduct', 'other'])->default('other');
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['open', 'under_review', 'resolved', 'dismissed'])->default('open')->index();
            $table->text('resolution')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
