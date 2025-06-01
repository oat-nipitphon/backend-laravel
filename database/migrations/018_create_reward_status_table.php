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
        Schema::create('reward_status', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->binary('icon')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE reward_status MODIFY icon LONGBLOB NULL');
                 DB::table('reward_status')->insert([
            [
                'id' => 1,
                'name' => 'active'
            ],
            [
                'id' => 2,
                'name' => 'comming_soon'
            ],
            [
                'id' => 3,
                'name' => 'disabled'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_status');
    }
};
