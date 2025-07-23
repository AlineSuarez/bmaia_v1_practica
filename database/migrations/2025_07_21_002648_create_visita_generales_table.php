<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitaGeneralesTable extends Migration
{
    public function up()
    {
        Schema::create('visita_generales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visita_id')->constrained('visitas')->onDelete('cascade');
            $table->string('motivo')->nullable();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('rut')->nullable();
            $table->string('telefono')->nullable();
            $table->string('firma')->nullable();
            $table->text('observacion_primera_visita')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visita_generales');
    }
}
