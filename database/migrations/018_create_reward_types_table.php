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
        Schema::create('reward_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->binary('icon')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE reward_types MODIFY icon LONGBLOB NULL');
                 DB::table('reward_types')->insert([
            [
                'id' => 1,
                'name' => 'reward'
            ],
            [
                'id' => 2,
                'name' => 'bonus'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_types');
    }
};
