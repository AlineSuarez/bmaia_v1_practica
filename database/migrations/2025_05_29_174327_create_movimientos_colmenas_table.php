<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movimientos_colmenas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('colmena_id');
            $table->unsignedBigInteger('apiario_origen_id');
            $table->unsignedBigInteger('apiario_destino_id');
            $table->enum('tipo_movimiento', ['traslado', 'retorno']);
            $table->dateTime('fecha_movimiento');
            $table->date('fecha_inicio_mov')->nullable();
            $table->date('fecha_termino_mov')->nullable();
            $table->string('motivo_movimiento')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('transportista')->nullable();
            $table->string('vehiculo')->nullable();
            $table->timestamps();

            $table->foreign('colmena_id')->references('id')->on('colmenas')->onDelete('cascade');
            $table->foreign('apiario_origen_id')->references('id')->on('apiarios')->onDelete('cascade');
            $table->foreign('apiario_destino_id')->references('id')->on('apiarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_colmenas');
    }
};

