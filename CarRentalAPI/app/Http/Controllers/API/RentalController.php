<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use App\Models\Car;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        return Rental::with('car')->get();
    }

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

        return new RentalResource($rental->load('car'));
    }

    public function show(Rental $rental)
    {
        return new RentalResource($rental->load('car'));
    }

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

        return new RentalResource($rental->load('car'));
    }

    public function destroy(Rental $rental)
    {
        // Rendre la voiture disponible à nouveau
        $rental->car->update(['is_available' => true]);
        
        $rental->delete();
        return response()->json(null, 204);
    }
}
