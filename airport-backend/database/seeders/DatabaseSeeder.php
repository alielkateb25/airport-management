<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Airport;
use App\Models\Airline;
use App\Models\Aircraft;
use App\Models\Passenger;
use App\Models\Flight;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@airport.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@airport.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $agent = User::create([
            'name' => 'Agent User',
            'email' => 'agent@airport.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'is_active' => true,
        ]);

        // Create Airports (8 airports)
        $airports = collect([
            ['code' => 'JFK', 'name' => 'John F. Kennedy International Airport', 'city' => 'New York', 'country' => 'USA', 'timezone' => 'America/New_York'],
            ['code' => 'LAX', 'name' => 'Los Angeles International Airport', 'city' => 'Los Angeles', 'country' => 'USA', 'timezone' => 'America/Los_Angeles'],
            ['code' => 'LHR', 'name' => 'London Heathrow Airport', 'city' => 'London', 'country' => 'UK', 'timezone' => 'Europe/London'],
            ['code' => 'CDG', 'name' => 'Charles de Gaulle Airport', 'city' => 'Paris', 'country' => 'France', 'timezone' => 'Europe/Paris'],
            ['code' => 'DXB', 'name' => 'Dubai International Airport', 'city' => 'Dubai', 'country' => 'UAE', 'timezone' => 'Asia/Dubai'],
            ['code' => 'SIN', 'name' => 'Singapore Changi Airport', 'city' => 'Singapore', 'country' => 'Singapore', 'timezone' => 'Asia/Singapore'],
            ['code' => 'HND', 'name' => 'Tokyo Haneda Airport', 'city' => 'Tokyo', 'country' => 'Japan', 'timezone' => 'Asia/Tokyo'],
            ['code' => 'FRA', 'name' => 'Frankfurt Airport', 'city' => 'Frankfurt', 'country' => 'Germany', 'timezone' => 'Europe/Berlin'],
        ])->map(function ($airport) {
            return Airport::create([...$airport, 'status' => 'active']);
        });

        // Create Airlines (8 airlines)
        $airlines = collect([
            ['code' => 'AA', 'name' => 'American Airlines', 'country' => 'USA'],
            ['code' => 'BA', 'name' => 'British Airways', 'country' => 'UK'],
            ['code' => 'EK', 'name' => 'Emirates', 'country' => 'UAE'],
            ['code' => 'QR', 'name' => 'Qatar Airways', 'country' => 'Qatar'],
            ['code' => 'LH', 'name' => 'Lufthansa', 'country' => 'Germany'],
            ['code' => 'SQ', 'name' => 'Singapore Airlines', 'country' => 'Singapore'],
            ['code' => 'AF', 'name' => 'Air France', 'country' => 'France'],
            ['code' => 'DL', 'name' => 'Delta Airlines', 'country' => 'USA'],
        ])->map(function ($airline) {
            return Airline::create([
                ...$airline, 
                'contact_email' => strtolower($airline['code']) . '@airline.com',
                'status' => 'active'
            ]);
        });

        // Create Aircraft (15 aircraft)
        $aircraftModels = [
            'Boeing 737-800',
            'Boeing 777-300ER',
            'Airbus A320',
            'Airbus A350-900',
            'Boeing 787-9 Dreamliner',
            'Airbus A380-800',
        ];

        $aircraft = collect();
        foreach (range(1, 15) as $i) {
            $aircraft->push(Aircraft::create([
                'airline_id' => $airlines->random()->id,
                'model' => $aircraftModels[array_rand($aircraftModels)],
                'registration_number' => strtoupper(fake()->bothify('??-###')),
                'capacity_economy' => fake()->numberBetween(100, 200),
                'capacity_business' => fake()->numberBetween(20, 50),
                'capacity_first' => fake()->numberBetween(8, 16),
                'status' => 'active',
            ]));
        }

        // Create Passengers (30 passengers)
        $passengers = collect();
        foreach (range(1, 30) as $i) {
            $passengers->push(Passenger::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'passport_number' => strtoupper(fake()->bothify('??######')),
                'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
                'nationality' => fake()->country(),
            ]));
        }

        // Create Flights (25 flights)
        $flights = collect();
        foreach (range(1, 25) as $i) {
            $origin = $airports->random();
            $destination = $airports->where('id', '!=', $origin->id)->random();
            $departureTime = fake()->dateTimeBetween('now', '+30 days');
            $arrivalTime = (clone $departureTime)->modify('+' . fake()->numberBetween(2, 12) . ' hours');

            $flights->push(Flight::create([
                'flight_number' => strtoupper(fake()->bothify('??###')),
                'airline_id' => $airlines->random()->id,
                'aircraft_id' => $aircraft->random()->id,
                'origin_airport_id' => $origin->id,
                'destination_airport_id' => $destination->id,
                'departure_time' => $departureTime,
                'arrival_time' => $arrivalTime,
                'base_price' => fake()->randomFloat(2, 100, 1500),
                'status' => fake()->randomElement(['scheduled', 'scheduled', 'scheduled', 'delayed']),
            ]));
        }

        // Create Bookings (40 bookings)
        foreach (range(1, 40) as $i) {
            $class = fake()->randomElement(['economy', 'business', 'first']);
            $basePrice = fake()->randomFloat(2, 200, 1000);
            
            $multiplier = match($class) {
                'economy' => 1,
                'business' => 2,
                'first' => 3,
            };

            $booking = Booking::create([
                'booking_reference' => 'BK' . strtoupper(\Illuminate\Support\Str::random(8)),
                'passenger_id' => $passengers->random()->id,
                'flight_id' => $flights->random()->id,
                'booked_by' => collect([$admin, $staff, $agent])->random()->id,
                'seat_number' => fake()->bothify('#?'),
                'class' => $class,
                'booking_date' => fake()->dateTimeBetween('-30 days', 'now'),
                'total_price' => $basePrice * $multiplier,
                'status' => fake()->randomElement(['confirmed', 'confirmed', 'pending']),
            ]);

            // Create Payment for confirmed bookings
            if ($booking->status === 'confirmed') {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'payment_method' => fake()->randomElement(['credit_card', 'debit_card', 'bank_transfer']),
                    'transaction_id' => 'TXN' . strtoupper(\Illuminate\Support\Str::random(12)),
                    'payment_date' => $booking->booking_date,
                    'status' => 'completed',
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Users created: Admin, Staff, Agent (password: password)');
        $this->command->info("Airports: {$airports->count()}");
        $this->command->info("Airlines: {$airlines->count()}");
        $this->command->info("Aircraft: {$aircraft->count()}");
        $this->command->info("Passengers: {$passengers->count()}");
        $this->command->info("Flights: {$flights->count()}");
        $this->command->info("Bookings: 40");
    }
}