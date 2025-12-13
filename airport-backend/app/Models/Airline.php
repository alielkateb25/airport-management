<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'country',
        'contact_email',
        'status',
    ];

    // Relationships
    public function aircraft()
    {
        return $this->hasMany(Aircraft::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}