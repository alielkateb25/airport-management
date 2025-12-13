<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['passenger', 'flight.airline', 'flight.originAirport', 'flight.destinationAirport', 'bookedBy']);

        // For agents, show only their bookings
        if ($request->user()->role === 'agent') {
            $query->where('booked_by', $request->user()->id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('passenger', function($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by flight
        if ($request->has('flight_id')) {
            $query->where('flight_id', $request->flight_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->paginate(15);

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'passenger_id' => 'required|exists:passengers,id',
            'flight_id' => 'required|exists:flights,id',
            'seat_number' => 'nullable|string|max:10',
            'class' => 'required|in:economy,business,first',
            'total_price' => 'required|numeric|min:0',
        ]);

        // Check if flight has available seats
        $flight = Flight::findOrFail($validated['flight_id']);
        
        if ($flight->available_seats <= 0) {
            return response()->json([
                'message' => 'No available seats on this flight',
            ], 422);
        }

        // Check if seat number is already taken
        if ($validated['seat_number']) {
            $seatTaken = Booking::where('flight_id', $validated['flight_id'])
                ->where('seat_number', $validated['seat_number'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($seatTaken) {
                return response()->json([
                    'message' => 'Seat number already taken',
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $booking = Booking::create([
                'passenger_id' => $validated['passenger_id'],
                'flight_id' => $validated['flight_id'],
                'booked_by' => $request->user()->id,
                'seat_number' => $validated['seat_number'],
                'class' => $validated['class'],
                'booking_date' => now(),
                'total_price' => $validated['total_price'],
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => $booking->load(['passenger', 'flight.airline', 'flight.originAirport', 'flight.destinationAirport']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Booking $booking)
    {
        // Check authorization
        if ($booking->booked_by !== request()->user()->id && !request()->user()->isAdmin() && !request()->user()->isStaff()) {
            return response()->json([
                'message' => 'Unauthorized to view this booking',
            ], 403);
        }

        return response()->json($booking->load([
            'passenger', 
            'flight.airline', 
            'flight.aircraft',
            'flight.originAirport', 
            'flight.destinationAirport',
            'payment',
            'bookedBy'
        ]));
    }

    public function update(Request $request, Booking $booking)
    {
        // Only allow updates to pending bookings
        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot update cancelled booking',
            ], 422);
        }

        $validated = $request->validate([
            'seat_number' => 'nullable|string|max:10',
            'class' => 'sometimes|in:economy,business,first',
            'total_price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:confirmed,pending,cancelled',
        ]);

        // Check if new seat number is available
        if (isset($validated['seat_number']) && $validated['seat_number'] !== $booking->seat_number) {
            $seatTaken = Booking::where('flight_id', $booking->flight_id)
                ->where('seat_number', $validated['seat_number'])
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $booking->id)
                ->exists();

            if ($seatTaken) {
                return response()->json([
                    'message' => 'Seat number already taken',
                ], 422);
            }
        }

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $booking->load(['passenger', 'flight.airline', 'flight.originAirport', 'flight.destinationAirport']),
        ]);
    }

    public function destroy(Booking $booking)
    {
        // Only allow cancellation, not deletion
        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Booking already cancelled',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
        ]);
    }
}