<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flora_species', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('common_name');        // Nombre común (Quillay, Tevo, etc.)
            $table->string('scientific_name')->nullable();
            $table->string('family')->nullable();
            $table->string('origin')->nullable();        // Endémica, Introducida, Nativa
            $table->string('growth_habit')->nullable();  // Arbóreo, Arbusto, Herbácea, etc.

            // Datos para futuros filtros (si quieres después)
            $table->string('nectar_type')->nullable();       // Néctar, Polen, Ambos...
            $table->string('growth_form')->nullable();       // Árbol, Arbusto, etc.
            $table->string('attraction_level')->nullable();  // Alto, Medio, Bajo
            $table->string('flowering_season')->nullable();  // Primavera, Primavera-Verano, etc.

            // Contenido de la ficha
            $table->text('description')->nullable();
            $table->text('habitat')->nullable();
            $table->text('uses')->nullable();       // Medicinal, melífera, etc. (opcional)

            // Imagen principal
            $table->string('image_path')->nullable(); // ej: flora/tevo.jpg en storage/app/public

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flora_species');
    }
};
