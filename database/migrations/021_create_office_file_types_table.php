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
        Schema::create('office_file_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->binary('icon')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE office_file_types MODIFY icon LONGBLOB NULL');
        DB::table('office_file_types')->insert([
            [
                'id' => 1,
                'name' => 'word'
            ],
            [
                'id' => 2,
                'name' => 'power_point'
            ],
            [
                'id' => 3,
                'name' => 'pdf'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_file_types');
    }
};
