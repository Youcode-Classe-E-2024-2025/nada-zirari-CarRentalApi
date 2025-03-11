<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        return response()->json(Car::all());
    }

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
        return response()->json($car, 201);
    }

    public function show(Car $car)
    {
        return response()->json($car);
    }

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
        return response()->json($car);
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return response()->json(null, 204);
    }
}
