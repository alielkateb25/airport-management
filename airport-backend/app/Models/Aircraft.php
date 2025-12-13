<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'airline_id',
        'model',
        'registration_number',
        'capacity_economy',
        'capacity_business',
        'capacity_first',
        'status',
    ];

    protected $casts = [
        'capacity_economy' => 'integer',
        'capacity_business' => 'integer',
        'capacity_first' => 'integer',
    ];

    // Relationships
    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    // Accessor for total capacity
    public function getTotalCapacityAttribute()
    {
        return $this->capacity_economy + $this->capacity_business + $this->capacity_first;
    }
}