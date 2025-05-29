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
        Schema::create('post_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->binary('icon')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE post_types MODIFY icon LONGBLOB NULL');
         DB::table('post_types')->insert([
            [
                'id' => 1,
                'name' => 'laravel'
            ],
            [
                'id' => 2,
                'name' => 'vue'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_types');
    }
};
