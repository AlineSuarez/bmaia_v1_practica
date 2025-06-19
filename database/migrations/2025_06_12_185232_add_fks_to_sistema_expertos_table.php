<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFksToSistemaExpertosTable extends Migration
{
    public function up()
    {
        Schema::table('sistema_expertos', function (Blueprint $t) {
            // 1) Primero eliminamos las FKs existentes
            $t->dropForeign(['apiario_id']);
            $t->dropForeign(['colmena_id']);
            $t->dropForeign(['desarrollo_cria_id']);
            $t->dropForeign(['calidad_reina_id']);
            $t->dropForeign(['estado_nutricional_id']);
            $t->dropForeign(['presencia_varroa_id']);
            $t->dropForeign(['presencia_nosemosis_id']);
            $t->dropForeign(['indice_cosecha_id']);
            $t->dropForeign(['preparacion_invernada_id']);

            // 2) Ahora las volvemos a crear
            $t->foreign('apiario_id')
              ->references('id')->on('apiarios')
              ->cascadeOnDelete();

            $t->foreign('colmena_id')
              ->references('id')->on('colmenas')
              ->cascadeOnDelete();

            $t->foreign('desarrollo_cria_id')
              ->references('id')->on('desarrollo_cria')
              ->cascadeOnDelete();

            $t->foreign('calidad_reina_id')
              ->references('id')->on('calidad_reina')
              ->cascadeOnDelete();

            $t->foreign('estado_nutricional_id')
              ->references('id')->on('estado_nutricional')
              ->cascadeOnDelete();

            $t->foreign('presencia_varroa_id')
              ->references('id')->on('presencia_varroa')
              ->cascadeOnDelete();

            $t->foreign('presencia_nosemosis_id')
              ->references('id')->on('presencia_nosemosis')
              ->cascadeOnDelete();

            $t->foreign('indice_cosecha_id')
              ->references('id')->on('indice_cosecha')
              ->cascadeOnDelete();

            $t->foreign('preparacion_invernada_id')
              ->references('id')->on('preparacion_invernada')
              ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('sistema_expertos', function (Blueprint $t) {
            $t->dropForeign(['apiario_id']);
            $t->dropForeign(['colmena_id']);
            $t->dropForeign(['desarrollo_cria_id']);
            $t->dropForeign(['calidad_reina_id']);
            $t->dropForeign(['estado_nutricional_id']);
            $t->dropForeign(['presencia_varroa_id']);
            $t->dropForeign(['presencia_nosemosis_id']);
            $t->dropForeign(['indice_cosecha_id']);
            $t->dropForeign(['preparacion_invernada_id']);
        });
    }
}
