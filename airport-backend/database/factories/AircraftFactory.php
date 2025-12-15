<?php

namespace Database\Factories;

use App\Models\Airline;
use Illuminate\Database\Eloquent\Factories\Factory;

class AircraftFactory extends Factory
{
    public function definition(): array
    {
        $models = [
            'Boeing 737-800',
            'Boeing 777-300ER',
            'Airbus A320',
            'Airbus A350-900',
            'Boeing 787-9 Dreamliner',
            'Airbus A380-800',
        ];

        return [
            'airline_id' => Airline::factory(),
            'model' => $this->faker->randomElement($models),
            'registration_number' => strtoupper($this->faker->bothify('??-###')),
            'capacity_economy' => $this->faker->numberBetween(100, 200),
            'capacity_business' => $this->faker->numberBetween(20, 50),
            'capacity_first' => $this->faker->numberBetween(8, 16),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'maintenance']),
        ];
    }
}