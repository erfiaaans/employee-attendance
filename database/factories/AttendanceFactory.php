<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Location;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Attendance::class;
    public function definition(): array
    {
        return [
            'attendance_id' => 'ATT' . $this->faker->unique()->numerify('#####'),
            'user_id' => User::inRandomOrder()->first()->user_id ?? 'U001',
            'location_id' => Location::inRandomOrder()->first()->location_id ?? 'LOC001',
            'clock_in_time' => $this->faker->dateTimeThisMonth('09:00:00'),
            'clock_in_latitude' => $this->faker->latitude(-90, 90),
            'clock_in_longitude' => $this->faker->longitude(-180, 180),
            'clock_in_photo_url' => $this->faker->imageUrl(),

            'clock_out_time' => $this->faker->dateTimeThisMonth('17:00:00'),
            'clock_out_latitude' => $this->faker->latitude(-90, 90),
            'clock_out_longitude' => $this->faker->longitude(-180, 180),
            'clock_out_photo_url' => $this->faker->imageUrl(),

            'created_by' => 'admin',
            'updated_by' => null,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => null
        ];
    }
}
