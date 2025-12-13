<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.passenger', 'booking.flight']);

        // For agents, show only payments for their bookings
        if ($request->user()->role === 'agent') {
            $query->whereHas('booking', function($q) use ($request) {
                $q->where('booked_by', $request->user()->id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:credit_card,debit_card,cash,bank_transfer',
        ]);

        // Check if booking already has a payment
        $booking = Booking::findOrFail($validated['booking_id']);
        
        if ($booking->payment) {
            return response()->json([
                'message' => 'Booking already has a payment',
            ], 422);
        }

        // Verify payment amount matches booking total
        if ($validated['amount'] != $booking->total_price) {
            return response()->json([
                'message' => 'Payment amount does not match booking total',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'booking_id' => $validated['booking_id'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
                'payment_date' => now(),
                'status' => 'completed',
            ]);

            // Update booking status to confirmed
            $booking->update(['status' => 'confirmed']);

            DB::commit();

            return response()->json([
                'message' => 'Payment processed successfully',
                'payment' => $payment->load('booking'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Payment processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load(['booking.passenger', 'booking.flight']));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:completed,pending,refunded',
        ]);

        DB::beginTransaction();

        try {
            $payment->update($validated);

            // If refunded, update booking status
            if ($validated['status'] === 'refunded') {
                $payment->booking->update(['status' => 'cancelled']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment updated successfully',
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Payment $payment)
    {
        return response()->json([
            'message' => 'Payment records cannot be deleted',
        ], 403);
    }
}