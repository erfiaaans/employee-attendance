<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // php artisan db:wipe -> menghapus semua database
    // php artisan migrate -> buat database
    // php artisan migrate:refresh -> update refresh
    // php artisan db:seed --class=DummySeeder -> seeding
    public function run()
    {
        $users = [
            [
                'user_id' => 'USR001',
                'name' => 'Admin User',
                'role' => 'admin',
                'profile_picture_url' => null,
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'gender' => 'male',
                'telephone' => '081234567890',
                'city' => 'Jakarta',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'password' => bcrypt('password'),
                'position' => 'Administrator',
            ],
            [
                'user_id' => 'USR002',
                'name' => 'Employee User',
                'role' => 'employee',
                'profile_picture_url' => null,
                'email' => 'employee@gmail.com',
                'email_verified_at' => now(),
                'gender' => 'female',
                'telephone' => '089876543210',
                'city' => 'Bandung',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'password' => bcrypt('password'),
                'position' => 'Staff',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }

        DB::table('locations')->insert([
            [
                'location_id' => 'LOC001',
                'office_name' => 'PT Surabaya Maju Jaya',
                'address' => 'Jl. Tunjungan No.1, Surabaya',
                'city' => 'Surabaya',
                'latitude' => -7.26575756,
                'longitude' => 112.73439884,
                'radius' => 100,
                'check_in_time' => '08:00:00',
                'check_out_time' => '16:30:00',
                'created_by' => 'admin',
                'updated_by' => 'admin',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'location_id' => 'LOC002',
                'office_name' => 'PT Jogja Sejahtera',
                'address' => 'Jl. Malioboro No.45, Yogyakarta',
                'city' => 'Yogyakarta',
                'latitude' => -7.79277800,
                'longitude' => 110.36588300,
                'radius' => 100,
                'check_in_time' => '08:00:00',
                'check_out_time' => '16:30:00',
                'created_by' => 'admin',
                'updated_by' => 'admin',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'location_id' => 'LOC003',
                'office_name' => 'PT Jakarta Sentosa',
                'address' => 'Jl. MH Thamrin No.10, Jakarta Pusat',
                'city' => 'Jakarta',
                'latitude' => -6.19514700,
                'longitude' => 106.82199800,
                'radius' => 100,
                'check_in_time' => '08:00:00',
                'check_out_time' => '16:30:00',
                'created_by' => 'admin',
                'updated_by' => 'admin',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ]);
    }
}
