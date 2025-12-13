<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Airport;
use App\Models\Airline;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create users
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@airport.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@airport.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Agent User',
            'email' => 'agent@airport.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'is_active' => true,
        ]);

        // Create airports
        Airport::create([
            'code' => 'JFK',
            'name' => 'John F. Kennedy International Airport',
            'city' => 'New York',
            'country' => 'USA',
            'timezone' => 'America/New_York',
            'status' => 'active',
        ]);

        Airport::create([
            'code' => 'LAX',
            'name' => 'Los Angeles International Airport',
            'city' => 'Los Angeles',
            'country' => 'USA',
            'timezone' => 'America/Los_Angeles',
            'status' => 'active',
        ]);

        Airport::create([
            'code' => 'LHR',
            'name' => 'London Heathrow Airport',
            'city' => 'London',
            'country' => 'UK',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        // Create airlines
        Airline::create([
            'code' => 'AA',
            'name' => 'American Airlines',
            'country' => 'USA',
            'contact_email' => 'contact@aa.com',
            'status' => 'active',
        ]);

        Airline::create([
            'code' => 'BA',
            'name' => 'British Airways',
            'country' => 'UK',
            'contact_email' => 'contact@ba.com',
            'status' => 'active',
        ]);
    }
}