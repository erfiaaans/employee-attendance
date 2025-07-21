<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate dan seeding
        User::truncate();
        User::factory()->count(10)->create();

        // Aktifkan lagi foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
