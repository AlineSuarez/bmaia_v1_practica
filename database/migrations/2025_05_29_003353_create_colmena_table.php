<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Deshabilita momentáneamente las FK para evitar errores al dropear
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('colmenas');
        Schema::enableForeignKeyConstraints();

        Schema::create('colmenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apiario_id')->constrained()->onDelete('cascade');
            $table->string('codigo_qr')->unique();
            $table->string('nombre');
            // Ahora permitimos null en estado_inicial y numero_marcos:
            $table->string('estado_inicial')->nullable();
            $table->integer('numero_marcos')->nullable();
            $table->string('color_etiqueta')->nullable();
            $table->string('numero')->nullable();
            $table->text('observaciones')->nullable();
            $table->json('historial')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        // En down volvemos a eliminar la tabla
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('colmenas');
        Schema::enableForeignKeyConstraints();
    }

};
