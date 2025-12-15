<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AirlineFactory extends Factory
{
    public function definition(): array
    {
        $airlines = [
            ['code' => 'AA', 'name' => 'American Airlines', 'country' => 'USA'],
            ['code' => 'BA', 'name' => 'British Airways', 'country' => 'UK'],
            ['code' => 'EK', 'name' => 'Emirates', 'country' => 'UAE'],
            ['code' => 'QR', 'name' => 'Qatar Airways', 'country' => 'Qatar'],
            ['code' => 'LH', 'name' => 'Lufthansa', 'country' => 'Germany'],
            ['code' => 'SQ', 'name' => 'Singapore Airlines', 'country' => 'Singapore'],
            ['code' => 'AF', 'name' => 'Air France', 'country' => 'France'],
            ['code' => 'DL', 'name' => 'Delta Airlines', 'country' => 'USA'],
        ];

        $airline = $this->faker->randomElement($airlines);

        return [
            'code' => $airline['code'],
            'name' => $airline['name'],
            'country' => $airline['country'],
            'contact_email' => strtolower($airline['code']) . '@airline.com',
            'status' => 'active',
        ];
    }
}