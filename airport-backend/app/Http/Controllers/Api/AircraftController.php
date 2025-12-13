<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use Illuminate\Http\Request;

class AircraftController extends Controller
{
    public function index(Request $request)
    {
        $query = Aircraft::with('airline');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        // Filter by airline
        if ($request->has('airline_id')) {
            $query->where('airline_id', $request->airline_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $aircraft = $query->orderBy('registration_number')->paginate(15);

        return response()->json($aircraft);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'model' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:aircraft',
            'capacity_economy' => 'required|integer|min:0',
            'capacity_business' => 'required|integer|min:0',
            'capacity_first' => 'required|integer|min:0',
            'status' => 'required|in:active,maintenance,retired',
        ]);

        // Validate that at least one capacity is greater than 0
        if ($validated['capacity_economy'] + $validated['capacity_business'] + $validated['capacity_first'] == 0) {
            return response()->json([
                'message' => 'Aircraft must have at least one seat',
            ], 422);
        }

        $aircraft = Aircraft::create($validated);

        return response()->json([
            'message' => 'Aircraft created successfully',
            'aircraft' => $aircraft->load('airline'),
        ], 201);
    }

    public function show(Aircraft $aircraft)
    {
        return response()->json($aircraft->load(['airline', 'flights']));
    }

    public function update(Request $request, Aircraft $aircraft)
    {
        $validated = $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'model' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:aircraft,registration_number,' . $aircraft->id,
            'capacity_economy' => 'required|integer|min:0',
            'capacity_business' => 'required|integer|min:0',
            'capacity_first' => 'required|integer|min:0',
            'status' => 'required|in:active,maintenance,retired',
        ]);

        // Validate that at least one capacity is greater than 0
        if ($validated['capacity_economy'] + $validated['capacity_business'] + $validated['capacity_first'] == 0) {
            return response()->json([
                'message' => 'Aircraft must have at least one seat',
            ], 422);
        }

        $aircraft->update($validated);

        return response()->json([
            'message' => 'Aircraft updated successfully',
            'aircraft' => $aircraft->load('airline'),
        ]);
    }

    public function destroy(Aircraft $aircraft)
    {
        // Check if aircraft has scheduled flights
        $hasScheduledFlights = $aircraft->flights()
            ->whereIn('status', ['scheduled', 'delayed'])
            ->exists();

        if ($hasScheduledFlights) {
            return response()->json([
                'message' => 'Cannot delete aircraft with scheduled or delayed flights',
            ], 422);
        }

        $aircraft->delete();

        return response()->json([
            'message' => 'Aircraft deleted successfully',
        ]);
    }
}