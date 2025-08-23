<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->string('attendance_id')->primary();

            $table->string('user_id');
            $table->string('location_id')->nullable();

            $table->dateTime('clock_in_time')->nullable();
            $table->string('clock_in_photo_url')->nullable();
            $table->decimal('clock_in_latitude', 10, 8)->nullable();
            $table->decimal('clock_in_longitude', 11, 8)->nullable();

            $table->dateTime('clock_out_time')->nullable();
            $table->string('clock_out_photo_url')->nullable();
            $table->decimal('clock_out_latitude', 10, 8)->nullable();
            $table->decimal('clock_out_longitude', 11, 8)->nullable();

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('location_id')->references('location_id')->on('locations')->onDelete('no action');

            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->string('created_at', 36)->nullable();
            $table->string('updated_at', 36)->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
