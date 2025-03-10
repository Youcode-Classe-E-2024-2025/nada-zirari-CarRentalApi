<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarController extends Controller
{
   

   


   
    public function index() {
        return Car::all();  // Liste de toutes les voitures
    }
    
    public function store(Request $request) {
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer',
            'price_per_day' => 'required|numeric',
        ]);
        return Car::create($validated);
    }
    
    public function show(Car $car) {
        return $car;  // Détails d’une voiture spécifique
    }
    
    public function update(Request $request, Car $car) {
        $car->update($request->all());
        return $car;
    }
    
    public function destroy(Car $car) {
        $car->delete();
        return response()->noContent();
    }

    
}
