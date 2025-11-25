<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('region_perfiles', function (Blueprint $table) {
            $table->id();

            // Clave a tabla 'regiones' (ya existe en tu BD)
            $table->unsignedBigInteger('region_id')->unique();

            // Coberturas (ejemplo inicial, puedes ampliar sin romper el front)
            $table->decimal('bosque_nativo_ha', 14, 2)->nullable();
            $table->decimal('plantaciones_forestales_ha', 14, 2)->nullable();
            $table->decimal('bosque_nativo_pct', 6, 3)->nullable();
            $table->decimal('plantaciones_forestales_pct', 6, 3)->nullable();

            // Matriz de riesgo (placeholders iniciales)
            $table->decimal('amenaza_actual', 10, 4)->nullable();
            $table->decimal('amenaza_futuro', 10, 4)->nullable();
            $table->decimal('exposicion', 10, 4)->nullable();
            $table->decimal('sensibilidad', 10, 4)->nullable();
            $table->decimal('riesgo_actual', 10, 4)->nullable();
            $table->decimal('riesgo_futuro', 10, 4)->nullable();

            // Texto libre
            $table->text('resumen')->nullable();

            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regiones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('region_perfiles');
    }
};
