<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSistemaExpertosTable extends Migration
{
    public function up()
    {
        Schema::create('sistema_expertos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('apiario_id')
              ->constrained()
              ->cascadeOnDelete();
            $t->foreignId('colmena_id')
              ->constrained()
              ->cascadeOnDelete();
            $t->timestamp('fecha')->useCurrent();
            // FKs a PCC (ya creadas)
            $t->foreignId('desarrollo_cria_id')->nullable()
                  ->constrained('desarrollo_cria')->cascadeOnDelete();
            $t->foreignId('calidad_reina_id')->nullable()
                  ->constrained('calidad_reina')->cascadeOnDelete();
            $t->foreignId('estado_nutricional_id')->nullable()
                  ->constrained('estado_nutricional')->cascadeOnDelete();
            $t->foreignId('presencia_varroa_id')->nullable()
                  ->constrained('presencia_varroa')->cascadeOnDelete();
            $t->foreignId('presencia_nosemosis_id')->nullable()
                  ->constrained('presencia_nosemosis')->cascadeOnDelete();
            $t->foreignId('indice_cosecha_id')->nullable()
                  ->constrained('indice_cosecha')->cascadeOnDelete();
            $t->foreignId('preparacion_invernada_id')->nullable()
                  ->constrained('preparacion_invernada')->cascadeOnDelete();
            $t->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sistema_expertos');
    }
}
