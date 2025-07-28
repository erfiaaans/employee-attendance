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
        Schema::create('office_location_user', function (Blueprint $table) {
            $table->string('location_user_id', 36)->primary();
            $table->string('location_id', 36);
            $table->string('user_id', 36);

            $table->foreign('location_id')->references('location_id')->on('locations')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unique(['location_id', 'user_id']);

            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->string('created_at', 36)->nullable();
            $table->string('updated_at', 36)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_location_user');
    }
};
