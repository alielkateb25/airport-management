<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $query = Flight::with(['airline', 'aircraft', 'originAirport', 'destinationAirport']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('flight_number', 'like', "%{$search}%");
        }

        // Filter by airline
        if ($request->has('airline_id')) {
            $query->where('airline_id', $request->airline_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by origin airport
        if ($request->has('origin_airport_id')) {
            $query->where('origin_airport_id', $request->origin_airport_id);
        }

        // Filter by destination airport
        if ($request->has('destination_airport_id')) {
            $query->where('destination_airport_id', $request->destination_airport_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('departure_time', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('departure_time', '<=', $request->date_to);
        }

        $flights = $query->orderBy('departure_time', 'desc')->paginate(15);

        return response()->json($flights);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:10|unique:flights',
            'airline_id' => 'required|exists:airlines,id',
            'aircraft_id' => 'required|exists:aircraft,id',
            'origin_airport_id' => 'required|exists:airports,id',
            'destination_airport_id' => 'required|exists:airports,id|different:origin_airport_id',
            'departure_time' => 'required|date|after:now',
            'arrival_time' => 'required|date|after:departure_time',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,delayed,cancelled,completed',
        ]);

        $flight = Flight::create($validated);

        return response()->json([
            'message' => 'Flight created successfully',
            'flight' => $flight->load(['airline', 'aircraft', 'originAirport', 'destinationAirport']),
        ], 201);
    }

    public function show(Flight $flight)
    {
        return response()->json($flight->load([
            'airline', 
            'aircraft', 
            'originAirport', 
            'destinationAirport',
            'bookings.passenger'
        ]));
    }

    public function update(Request $request, Flight $flight)
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:10|unique:flights,flight_number,' . $flight->id,
            'airline_id' => 'required|exists:airlines,id',
            'aircraft_id' => 'required|exists:aircraft,id',
            'origin_airport_id' => 'required|exists:airports,id',
            'destination_airport_id' => 'required|exists:airports,id|different:origin_airport_id',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,delayed,cancelled,completed',
        ]);

        $flight->update($validated);

        return response()->json([
            'message' => 'Flight updated successfully',
            'flight' => $flight->load(['airline', 'aircraft', 'originAirport', 'destinationAirport']),
        ]);
    }

    public function destroy(Flight $flight)
    {
        // Check if flight has bookings
        if ($flight->bookings()->where('status', '!=', 'cancelled')->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete flight with active bookings',
            ], 422);
        }

        $flight->delete();

        return response()->json([
            'message' => 'Flight deleted successfully',
        ]);
    }

    public function search(Request $request)
    {
        $query = Flight::with(['airline', 'aircraft', 'originAirport', 'destinationAirport'])
            ->where('status', 'scheduled');

        if ($request->has('origin')) {
            $query->whereHas('originAirport', function($q) use ($request) {
                $q->where('code', $request->origin)
                  ->orWhere('city', 'like', "%{$request->origin}%");
            });
        }

        if ($request->has('destination')) {
            $query->whereHas('destinationAirport', function($q) use ($request) {
                $q->where('code', $request->destination)
                  ->orWhere('city', 'like', "%{$request->destination}%");
            });
        }

        if ($request->has('departure_date')) {
            $query->whereDate('departure_time', $request->departure_date);
        }

        if ($request->has('passengers')) {
            $query->whereHas('aircraft', function($q) use ($request) {
                $passengers = (int) $request->passengers;
                $q->whereRaw('(capacity_economy + capacity_business + capacity_first) >= ?', [$passengers]);
            });
        }

        $flights = $query->orderBy('departure_time')->get();

        // Add available seats to each flight
        $flights->each(function($flight) {
            $flight->available_seats = $flight->available_seats;
        });

        return response()->json($flights);
    }

    public function updateStatus(Request $request, Flight $flight)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,delayed,cancelled,completed',
        ]);

        $flight->update($validated);

        return response()->json([
            'message' => 'Flight status updated successfully',
            'flight' => $flight,
        ]);
    }
}