<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    public function index(Request $request)
    {
        $query = Passenger::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('passport_number', 'like', "%{$search}%");
            });
        }

        // Filter by nationality
        if ($request->has('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        $passengers = $query->orderBy('last_name')->paginate(15);

        return response()->json($passengers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'passport_number' => 'required|string|max:50|unique:passengers',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
        ]);

        $passenger = Passenger::create($validated);

        return response()->json([
            'message' => 'Passenger created successfully',
            'passenger' => $passenger,
        ], 201);
    }

    public function show(Passenger $passenger)
    {
        return response()->json($passenger->load('bookings.flight'));
    }

    public function update(Request $request, Passenger $passenger)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'passport_number' => 'required|string|max:50|unique:passengers,passport_number,' . $passenger->id,
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
        ]);

        $passenger->update($validated);

        return response()->json([
            'message' => 'Passenger updated successfully',
            'passenger' => $passenger,
        ]);
    }

    public function destroy(Passenger $passenger)
    {
        // Check if passenger has active bookings
        if ($passenger->bookings()->where('status', '!=', 'cancelled')->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete passenger with active bookings',
            ], 422);
        }

        $passenger->delete();

        return response()->json([
            'message' => 'Passenger deleted successfully',
        ]);
    }
}