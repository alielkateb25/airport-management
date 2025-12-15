<?php

namespace Database\Factories;

use App\Models\Airline;
use App\Models\Aircraft;
use App\Models\Airport;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlightFactory extends Factory
{
    public function definition(): array
    {
        $departureTime = $this->faker->dateTimeBetween('now', '+30 days');
        $arrivalTime = (clone $departureTime)->modify('+' . $this->faker->numberBetween(2, 12) . ' hours');

        return [
            'flight_number' => strtoupper($this->faker->bothify('??###')),
            'airline_id' => Airline::factory(),
            'aircraft_id' => Aircraft::factory(),
            'origin_airport_id' => Airport::factory(),
            'destination_airport_id' => Airport::factory(),
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
            'base_price' => $this->faker->randomFloat(2, 100, 1500),
            'status' => $this->faker->randomElement(['scheduled', 'scheduled', 'scheduled', 'delayed']),
        ];
    }
}