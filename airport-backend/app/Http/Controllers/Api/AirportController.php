<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index(Request $request)
    {
        $query = Airport::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $airports = $query->orderBy('name')->paginate(15);

        return response()->json($airports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:airports',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $airport = Airport::create($validated);

        return response()->json([
            'message' => 'Airport created successfully',
            'airport' => $airport,
        ], 201);
    }

    public function show(Airport $airport)
    {
        return response()->json($airport);
    }

    public function update(Request $request, Airport $airport)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:airports,code,' . $airport->id,
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $airport->update($validated);

        return response()->json([
            'message' => 'Airport updated successfully',
            'airport' => $airport,
        ]);
    }

    public function destroy(Airport $airport)
    {
        $airport->delete();

        return response()->json([
            'message' => 'Airport deleted successfully',
        ]);
    }
}