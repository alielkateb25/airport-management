<?php

namespace Database\Factories;

use App\Models\Passenger;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $class = $this->faker->randomElement(['economy', 'business', 'first']);
        $basePrice = $this->faker->randomFloat(2, 200, 1000);
        
        $multiplier = match($class) {
            'economy' => 1,
            'business' => 2,
            'first' => 3,
        };

        return [
            'booking_reference' => 'BK' . strtoupper(Str::random(8)),
            'passenger_id' => Passenger::factory(),
            'flight_id' => Flight::factory(),
            'booked_by' => User::factory(),
            'seat_number' => $this->faker->bothify('#?'),
            'class' => $class,
            'booking_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'total_price' => $basePrice * $multiplier,
            'status' => $this->faker->randomElement(['confirmed', 'confirmed', 'pending']),
        ];
    }
}