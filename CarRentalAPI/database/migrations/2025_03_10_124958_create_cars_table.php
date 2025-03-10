<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('make');  // Marque  
            $table->string('model'); // Modèle  
            $table->year('year');    // Année dfabrication
            $table->decimal('price_per_day', 8, 2);  // Prix de location par jour
            $table->text('description')->nullable();  // Description de voiture
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
