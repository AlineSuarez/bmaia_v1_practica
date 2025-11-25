<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('flor_fase_duraciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flor_id');        // FK a flores.id
            $table->string('fase_clave', 20);             // 'inicio' | 'plena' | 'terminal' (medidos desde 'boton')
            $table->unsignedSmallInteger('offset_dias');  // días desde 'boton' hasta esta fase
            $table->string('fuente')->nullable();
            $table->text('nota')->nullable();
            $table->timestamps();

            $table->unique(['flor_id','fase_clave']);
            $table->foreign('flor_id')->references('id')->on('flores')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('flor_fase_duraciones');
    }
};
