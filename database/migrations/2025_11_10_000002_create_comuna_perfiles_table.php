<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comuna_perfiles', function (Blueprint $table) {
            $table->id();

            // Clave a tabla 'comunas' (ya existe en tu BD)
            $table->unsignedBigInteger('comuna_id')->unique();

            // Coberturas comunales
            $table->decimal('bosque_nativo_ha', 14, 2)->nullable();
            $table->decimal('plantaciones_forestales_ha', 14, 2)->nullable();
            $table->decimal('bosque_nativo_pct', 6, 3)->nullable();
            $table->decimal('plantaciones_forestales_pct', 6, 3)->nullable();

            // Indicadores extra (puedes ir sumando)
            $table->decimal('elevacion_promedio_m', 10, 2)->nullable();
            $table->decimal('precipitacion_anual_mm', 10, 2)->nullable();

            $table->text('notas')->nullable();

            $table->timestamps();

            $table->foreign('comuna_id')->references('id')->on('comunas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comuna_perfiles');
    }
};
