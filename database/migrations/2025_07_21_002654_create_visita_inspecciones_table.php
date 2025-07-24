<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitaInspeccionesTable extends Migration
{
    public function up()
    {
        Schema::create('visita_inspecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visita_id')->constrained('visitas')->onDelete('cascade');
            $table->integer('num_colmenas_totales')->nullable();
            $table->integer('num_colmenas_inspeccionadas')->nullable();
            $table->integer('num_colmenas_enfermas')->nullable();
            $table->integer('num_colmenas_activas')->nullable();
            $table->integer('num_colmenas_muertas')->nullable();
            $table->string('flujo_nectar_polen')->nullable();
            $table->string('nombre_revisor_apiario')->nullable();
            $table->string('sospecha_enfermedad')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visita_inspecciones');
    }
}

