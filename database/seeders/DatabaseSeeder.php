<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\User;
use App\Models\OfficeLocationUser;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */


    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan semua tabel yang saling berkaitan
        User::truncate();
        Location::truncate();
        Attendance::truncate();
        OfficeLocationUser::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UserSeeder::class,
            LocationSeeder::class,
            AttendanceSeeder::class,
        ]);   
    }
}
