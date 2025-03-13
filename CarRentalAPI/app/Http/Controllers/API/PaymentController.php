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

    /**
     * @OA\Post(
     *     path="/api/rentals/{rental}/payment-intent",
     *     summary="Create a new payment intent",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="rental",
     *         in="path",
     *         required=true,
     *         description="Rental ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment intent created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="clientSecret", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
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
                'id' => $paymentIntent->id,
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
 /**
     * @OA\Post(
     *     path="/api/rentals/{rental}/payments",
     *     summary="Store a new payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="rental",
     *         in="path",
     *         required=true,
     *         description="Rental ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_method", "transaction_id", "payment_intent_id"},
     *             @OA\Property(property="payment_method", type="string", enum={"credit_card", "debit_card", "paypal"}),
     *             @OA\Property(property="transaction_id", type="string"),
     *             @OA\Property(property="payment_intent_id", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="payment", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
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
/**
     * @OA\Get(
     *     path="/api/payments/{payment}",
     *     summary="Get payment details",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="payment",
     *         in="path",
     *         required=true,
     *         description="Payment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details retrieved successfully",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function show(Payment $payment)
    {
        return response()->json($payment->load('rental'));
    }

    /**
     * @OA\Get(
     *     path="/api/payments",
     *     summary="Get paginated list of payments",
     *     tags={"Payments"},
     *     @OA\Response(
     *         response=200,
     *         description="List of payments retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */

    public function index()
    {
        return response()->json(Payment::with('rental')->paginate(10));
    }
}
