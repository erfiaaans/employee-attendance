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
        Schema::create('attendances', function (Blueprint $table) {
            $table->string('attendance_id', 20)->primary();

            $table->string('user_id', 20);
            $table->string('location_id', 20);

            $table->dateTime('clock_in_time');
            $table->text('clock_in_photo_url');
            $table->decimal('clock_in_latitude', 10, 8);
            $table->decimal('clock_in_longitude', 11, 8);

            $table->dateTime('clock_out_time');
            $table->text('clock_out_photo_url');
            $table->decimal('clock_out_latitude', 10, 8);
            $table->decimal('clock_out_longitude', 11, 8);

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('location_id')->references('location_id')->on('locations');

            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('created_at', 20)->nullable();
            $table->string('updated_at', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
