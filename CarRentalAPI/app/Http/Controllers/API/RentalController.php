<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Car;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Rentals",
 *     description="API Endpoints for car rental management"
 * )
 */
class RentalController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/rentals",
     *     summary="Get list of all rentals",
     *     tags={"Rentals"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Rental"))
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function index()
    {
        Rental::paginate(10);
        return Rental::with('car')->get();
    }
/**
     * @OA\Post(
     *     path="/api/rentals",
     *     summary="Create a new rental",
     *     tags={"Rentals"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"car_id","customer_name","start_date","end_date"},
     *             @OA\Property(property="car_id", type="integer"),
     *             @OA\Property(property="customer_name", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rental created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'customer_name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Calculer le coût total
        $car = Car::findOrFail($validated['car_id']);
        $days = date_diff(date_create($validated['start_date']), date_create($validated['end_date']))->days;
        $total_cost = $car->daily_rate * $days;

        $rental = Rental::create([
            'car_id' => $validated['car_id'],
            'customer_name' => $validated['customer_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_cost' => $total_cost
        ]);

        // Mettre à jour la disponibilité de la voiture
        $car->update(['is_available' => false]);

        return $rental->load('car');
    }
     /**
     * @OA\Get(
     *     path="/api/rentals/{rental}",
     *     summary="Get rental details",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="rental",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found"
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */

    public function show(Rental $rental)
    {
        return $rental->load('car');
    }
 /**
     * @OA\Put(
     *     path="/api/rentals/{rental}",
     *     summary="Update rental details",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="rental",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="customer_name", type="string"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'customer_name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Recalculer le coût total
        $days = date_diff(date_create($validated['start_date']), date_create($validated['end_date']))->days;
        $total_cost = $rental->car->daily_rate * $days;

        $rental->update([
            'customer_name' => $validated['customer_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_cost' => $total_cost
        ]);

        return $rental->load('car');
    }
 /**
     * @OA\Delete(
     *     path="/api/rentals/{rental}",
     *     summary="Delete a rental",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="rental",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rental deleted successfully"
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
    public function destroy(Rental $rental)
    {
        // Rendre la voiture disponible à nouveau
        $rental->car->update(['is_available' => true]);
        
        $rental->delete();
        return response()->json(null, 204);
    }
}
