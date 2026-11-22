<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Profile photos are kept in the database rather than on disk, because the Render
 * web service has no persistent disk and uploaded files would vanish on each deploy.
 * They live in their own table so that listing users never loads the image data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_avatars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('mime', 50);
            $table->longText('data'); // base64-encoded image
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            // used to build a cache-busting photo URL without loading the image
            $table->timestamp('avatar_updated_at')->nullable()->after('dorm_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_updated_at');
        });
        Schema::dropIfExists('user_avatars');
    }
};
