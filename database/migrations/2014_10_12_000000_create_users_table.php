<?php

use App\Enums\UserRole;
use App\Enums\UserGender;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id', 20)->primary();
            // $table->string('location_id')->nullable();
            // $table->foreign('location_id')->references('location_id')->on('locations')->onDelete('set null');

            $table->string('name', 100);
            $table->enum('role', UserRole::values())->default('employee');
            $table->text('profile_picture_url')->nullable();
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('gender', UserGender::values())->nullable();
            $table->string('telephone', 15)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('created_at', 20)->nullable();
            $table->string('updated_at', 20)->nullable();
            $table->string('password', 255);
            $table->string('position', 100)->nullable();
            // $table->string('remember_token', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'user_id', 'name', 'role', 'profile-picture_url', 'email',
                'email_verified_at', 'gender', 'position', 'telephone', 'city',
                'password', 'created_by', 'updated_by', 'created_at', 'updated_at', 'location_id'
            ]);
        });
    }
};
