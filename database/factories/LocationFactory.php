<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Location::class;
    public function definition(): array
    {
        return [
            'location_id' => 'LOC' . $this->faker->unique()->numerify('###'),
            'office_name' => $this->faker->company(),
            'check_in_time' => $this->faker->time('08:00:00'),
            'check_out_time' => $this->faker->time('16:30:00'),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(-90, 90),
            'longitude' => $this->faker->longitude(-180, 180),
            'radius' => $this->faker->numberBetween(1, 100),
            'created_by' => 'admin',
            'updated_by' => null,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => null
        ];
    }
}
