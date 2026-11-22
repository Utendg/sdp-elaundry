<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Responsibilities granted to administrator accounts. The Head of Residence Life holds the
 * "admins" permission, which also grants every other responsibility.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('permission', 30);
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'permission']);
        });

        // Existing admins keep the access they had; the first admin becomes Head of Residence Life.
        $admins = DB::table('users')->where('role', 'admin')->orderBy('id')->pluck('id');
        foreach ($admins as $i => $id) {
            $permissions = ['workers', 'prices', 'dorms', 'orders', 'complaints'];
            if ($i === 0) {
                $permissions[] = 'admins';
            }
            foreach ($permissions as $permission) {
                DB::table('admin_permissions')->insert([
                    'user_id' => $id,
                    'permission' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_permissions');
    }
};
