<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // php artisan db:wipe -> menghapus semua database
    // php artisan migrate -> buat database
    // php artisan migrate:refresh -> update refresh
    // php artisan db:seed --class=DummySeeder -> seeding

    function randomHexColor()
    {
        return sprintf('%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255));
    }
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
                'gender' => 'Laki-laki',
                'telephone' => '081234567890',
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
                'gender' => 'Perempuan',
                'telephone' => '089876543210',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'password' => bcrypt('password'),
                'position' => 'Staff',
            ],
            [
                'user_id' => 'USR003',
                'name' => 'Erfia Nadia',
                'role' => 'employee',
                'profile_picture_url' => null,
                'email' => 'erfianadia05@gmail.com',
                'email_verified_at' => now(),
                'gender' => 'Perempuan',
                'telephone' => '089812345678',
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

        DB::table('office_location_user')->insert([
            [
                'location_user_id' => 'LU001',
                'location_id' => 'LOC001',
                'user_id' => 'USR002',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'location_user_id' => 'LU002',
                'location_id' => 'LOC001',
                'user_id' => 'USR003',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'location_user_id' => 'LU003',
                'location_id' => 'LOC002',
                'user_id' => 'USR003',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ]);


        $users = [
            'USR002' => ['LOC001'],
            'USR003' => ['LOC001', 'LOC002'],
        ];

        $startDate = Carbon::create(2025, 6, 1);
        $endDate = Carbon::create(2025, 8, 23);

        while ($startDate->lte($endDate)) {
            if ($startDate->isWeekday()) {
                foreach ($users as $userId => $locations) {
                    $locationId = count($locations) === 1
                        ? $locations[0]
                        : $locations[array_rand($locations)];

                    $clockIn = $startDate->copy()->setTime(8, rand(0, 59), rand(0, 59));
                    $clockOut = $startDate->copy()->setTime(17, rand(0, 59), rand(0, 59));

                    $photoUrl = "https://dummyimage.com/400x400/" . $this->randomHexColor() . "/" . $this->randomHexColor();

                    DB::table('attendances')->insert([
                        'attendance_id' => Str::uuid()->toString(),
                        'user_id' => $userId,
                        'location_id' => $locationId,
                        'clock_in_time' => $clockIn,
                        'clock_in_photo_url' => $photoUrl,
                        'clock_in_latitude' => rand(-725090000, -725044500) / 10000000,
                        'clock_in_longitude' => rand(1127688450, 1127692000) / 10000000,
                        'clock_out_time' => $clockOut,
                        'clock_out_photo_url' => $photoUrl,
                        'clock_out_latitude' => rand(-725090000, -725044500) / 10000000,
                        'clock_out_longitude' => rand(1127688450, 1127692000) / 10000000,
                        'created_by' => 'system',
                        'updated_by' => 'system',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $startDate->addDay();
        }
    }
}
