<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Cars",
 *     description="API Endpoints for car management"
 * )
 */
class CarController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/cars",
     *     summary="Get paginated list of cars",
     *     tags={"Cars"},
     *     @OA\Response(
     *         response=200,
     *         description="List of cars retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="brand", type="string"),
     *                     @OA\Property(property="model", type="string"),
     *                     @OA\Property(property="year", type="integer"),
     *                     @OA\Property(property="color", type="string"),
     *                     @OA\Property(property="daily_rate", type="number")
     *                 )
     *             ),
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
        return Car::paginate(10);
    }
 /**
     * @OA\Post(
     *     path="/api/cars",
     *     summary="Create a new car",
     *     tags={"Cars"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"brand","model","year","color","daily_rate"},
     *             @OA\Property(property="brand", type="string"),
     *             @OA\Property(property="model", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="daily_rate", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="brand", type="string"),
     *             @OA\Property(property="model", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="daily_rate", type="number")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'color' => 'required',
            'daily_rate' => 'required|numeric'
        ]);

        $car = Car::create($validated);
        return $car;
    }
 /**
     * @OA\Get(
     *     path="/api/cars/{car}",
     *     summary="Get car details",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         required=true,
     *         description="Car ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car details retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="brand", type="string"),
     *             @OA\Property(property="model", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="daily_rate", type="number")
     *         )
     *     )
     * )
     */
    public function show(Car $car)
    {
        return $car;
    }
 /**
     * @OA\Put(
     *     path="/api/cars/{car}",
     *     summary="Update car details",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         required=true,
     *         description="Car ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"brand","model","year","color","daily_rate"},
     *             @OA\Property(property="brand", type="string"),
     *             @OA\Property(property="model", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="daily_rate", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="brand", type="string"),
     *             @OA\Property(property="model", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="daily_rate", type="number")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'color' => 'required',
            'daily_rate' => 'required|numeric'
        ]);

        $car->update($validated);
        return $car;
    }
 /**
     * @OA\Delete(
     *     path="/api/cars/{car}",
     *     summary="Delete a car",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         required=true,
     *         description="Car ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Car deleted successfully"
     *     )
     * )
     */
    public function destroy(Car $car)
    {
        $car->delete();
        return response()->json(null, 204);
    }
}
