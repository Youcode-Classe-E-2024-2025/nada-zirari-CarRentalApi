<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Rental;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Rental $rental)
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $rental->total_cost * 100, // Convert to cents
                'currency' => 'eur',
                'metadata' => [
                    'rental_id' => $rental->id
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,debit_card,paypal',
            'transaction_id' => 'required|unique:payments',
            'payment_intent_id' => 'required|string'
        ]);

        try {
            // Verify the payment with Stripe
            $paymentIntent = PaymentIntent::retrieve($validated['payment_intent_id']);
            
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment not successful'], 400);
            }

            $payment = Payment::create([
                'rental_id' => $rental->id,
                'amount' => $rental->total_cost,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'],
                'stripe_payment_intent_id' => $validated['payment_intent_id'],
                'status' => 'completed'
            ]);

            return response()->json([
                'message' => 'Payment processed successfully',
                'payment' => $payment
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load('rental'));
    }

    public function index()
    {
        return response()->json(Payment::with('rental')->paginate(10));
    }
}
