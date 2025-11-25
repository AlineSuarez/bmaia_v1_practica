<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('flor_perfiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flor_id');                 // FK a flores.id (1–a–1)
            $table->string('nombre_comun_alt')->nullable();         // opcional (otros nombres)
            $table->text('resumen')->nullable();                    // 2–3 líneas para card
            $table->longText('descripcion')->nullable();            // descripción completa
            $table->string('habitat')->nullable();                  // bosque esclerófilo, matorral, etc.
            $table->string('distribucion')->nullable();             // zona/altitud/región
            $table->unsignedTinyInteger('nectar_score')->nullable(); // 0–5 (potencial néctar)
            $table->unsignedTinyInteger('polen_score')->nullable();  // 0–5 (potencial polen)
            $table->text('usos')->nullable();                       // manejo apícola/notas
            $table->string('fuente')->nullable();                   // “INIA 2022…”
            $table->string('enlace')->nullable();                   // URL de referencia
            $table->timestamps();

            $table->unique('flor_id');
            $table->foreign('flor_id')->references('id')->on('flores')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('flor_perfiles');
    }
};
