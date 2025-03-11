<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Rental;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,debit_card,paypal',
            'transaction_id' => 'required|unique:payments'
        ]);

        $payment = Payment::create([
            'rental_id' => $rental->id,
            'amount' => $rental->total_cost,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'],
            'status' => 'completed'
        ]);

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment' => $payment
        ], 201);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load('rental'));
    }

    public function index()
    {
        Payment::paginate(10);
        return response()->json(Payment::with('rental')->get());
    }
}
