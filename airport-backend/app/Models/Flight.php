<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'airline_id',
        'aircraft_id',
        'origin_airport_id',
        'destination_airport_id',
        'departure_time',
        'arrival_time',
        'base_price',
        'status',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'base_price' => 'decimal:2',
    ];

    // Relationships
    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function originAirport()
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    public function destinationAirport()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Get available seats
    public function getAvailableSeatsAttribute()
    {
        $totalCapacity = $this->aircraft->total_capacity;
        $bookedSeats = $this->bookings()->where('status', '!=', 'cancelled')->count();
        return $totalCapacity - $bookedSeats;
    }
}