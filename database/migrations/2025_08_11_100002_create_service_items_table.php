<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The official price list (per clothing item) maintained by admins.
     * This is the single source of truth for pricing, preventing overcharging.
     */
    public function up(): void
    {
        Schema::create('service_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            // The service performed on the item (improves on the proposal's single flat rate).
            $table->enum('service', ['wash', 'iron', 'wash_iron', 'dry_clean'])->default('wash_iron');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_items');
    }
};
