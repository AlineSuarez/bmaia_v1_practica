<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tareas_apiario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apiario_id')->constrained('apiarios')->onDelete('cascade');
            $table->string('categoria_tarea'); // Inspección, Sanidad, Alimentación, Manejo, Otro
            $table->string('tarea_especifica')->nullable();
            $table->text('accion_realizada')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_termino')->nullable();
            $table->string('proximo_seguimiento')->nullable(); // texto o fecha según preferencia
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas_apiario');
    }

};
