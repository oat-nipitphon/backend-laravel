<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profile_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('user_profiles')->onDelete('cascade');
            $table->foreignId('profile_id_followers')->constrained('user_profiles')->onDelete('cascade');
            $table->enum('status', ['true', 'false'])->default('false');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_followers');
    }
};
