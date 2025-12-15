<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AirportFactory extends Factory
{
    public function definition(): array
    {
        $cities = [
            ['code' => 'JFK', 'name' => 'John F. Kennedy International Airport', 'city' => 'New York', 'country' => 'USA', 'timezone' => 'America/New_York'],
            ['code' => 'LAX', 'name' => 'Los Angeles International Airport', 'city' => 'Los Angeles', 'country' => 'USA', 'timezone' => 'America/Los_Angeles'],
            ['code' => 'LHR', 'name' => 'London Heathrow Airport', 'city' => 'London', 'country' => 'UK', 'timezone' => 'Europe/London'],
            ['code' => 'CDG', 'name' => 'Charles de Gaulle Airport', 'city' => 'Paris', 'country' => 'France', 'timezone' => 'Europe/Paris'],
            ['code' => 'DXB', 'name' => 'Dubai International Airport', 'city' => 'Dubai', 'country' => 'UAE', 'timezone' => 'Asia/Dubai'],
            ['code' => 'SIN', 'name' => 'Singapore Changi Airport', 'city' => 'Singapore', 'country' => 'Singapore', 'timezone' => 'Asia/Singapore'],
            ['code' => 'HND', 'name' => 'Tokyo Haneda Airport', 'city' => 'Tokyo', 'country' => 'Japan', 'timezone' => 'Asia/Tokyo'],
            ['code' => 'FRA', 'name' => 'Frankfurt Airport', 'city' => 'Frankfurt', 'country' => 'Germany', 'timezone' => 'Europe/Berlin'],
        ];

        $airport = $this->faker->randomElement($cities);

        return [
            'code' => $airport['code'],
            'name' => $airport['name'],
            'city' => $airport['city'],
            'country' => $airport['country'],
            'timezone' => $airport['timezone'],
            'status' => 'active',
        ];
    }
}