<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $booking = Booking::factory()->create();

        return [
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => $this->faker->randomElement(['credit_card', 'debit_card', 'bank_transfer']),
            'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
            'payment_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status' => 'completed',
        ];
    }
}