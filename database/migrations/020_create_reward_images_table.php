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
        Schema::create('reward_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade');
            $table->binary('image_data')->nullable();
            $table->enum('status', ['active', 'disable', 'comming_zone'])->default('active');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE reward_images MODIFY image_data LONGBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_images');
    }
};
