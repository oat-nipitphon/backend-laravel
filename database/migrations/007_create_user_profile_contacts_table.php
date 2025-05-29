<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profile_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('user_profiles')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('detail')->nullable();
            $table->binary('image_data')->nullable();
            $table->enum('status', ['active', 'disable'])->default('active')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE user_profile_contacts MODIFY image_data LONGBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_contacts');
    }
};
