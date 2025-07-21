<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;
use App\Models\OfficeLocationUser;

class OfficeLocationUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $locations = Location::all();

        foreach ($users as $user) {
            OfficeLocationUser::create([
                'user_id'     => $user->user_id,
                'location_id' => $locations->random()->location_id,
            ]);
        }
    }
}
