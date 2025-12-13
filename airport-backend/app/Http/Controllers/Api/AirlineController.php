<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index(Request $request)
    {
        $query = Airline::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $airlines = $query->orderBy('name')->paginate(15);

        return response()->json($airlines);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:airlines',
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $airline = Airline::create($validated);

        return response()->json([
            'message' => 'Airline created successfully',
            'airline' => $airline,
        ], 201);
    }

    public function show(Airline $airline)
    {
        return response()->json($airline->load('aircraft'));
    }

    public function update(Request $request, Airline $airline)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:airlines,code,' . $airline->id,
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $airline->update($validated);

        return response()->json([
            'message' => 'Airline updated successfully',
            'airline' => $airline,
        ]);
    }

    public function destroy(Airline $airline)
    {
        $airline->delete();

        return response()->json([
            'message' => 'Airline deleted successfully',
        ]);
    }
}