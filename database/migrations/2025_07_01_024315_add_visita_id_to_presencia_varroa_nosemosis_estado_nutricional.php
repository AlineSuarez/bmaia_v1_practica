<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisitaIdToPresenciaVarroaNosemosisEstadoNutricional extends Migration
{
    public function up()
    {
        // 1) presencia_varroa
        Schema::table('presencia_varroa', function (Blueprint $table) {
            // crea la columna como nullable
            $table->unsignedBigInteger('visita_id')->nullable()->after('colmena_id');
            // agrega la FK
            $table->foreign('visita_id')
                  ->references('id')->on('visitas')
                  ->onDelete('cascade');
        });

        // 2) presencia_nosemosis
        Schema::table('presencia_nosemosis', function (Blueprint $table) {
            $table->unsignedBigInteger('visita_id')->nullable()->after('colmena_id');
            $table->foreign('visita_id')
                  ->references('id')->on('visitas')
                  ->onDelete('cascade');
        });

        // 3) estado_nutricional
        Schema::table('estado_nutricional', function (Blueprint $table) {
            $table->unsignedBigInteger('visita_id')->nullable()->after('colmena_id');
            $table->foreign('visita_id')
                  ->references('id')->on('visitas')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        // Para el rollback, elimina primero la FK y luego la columna

        Schema::table('estado_nutricional', function (Blueprint $table) {
            $table->dropForeign(['visita_id']);
            $table->dropColumn('visita_id');
        });

        Schema::table('presencia_nosemosis', function (Blueprint $table) {
            $table->dropForeign(['visita_id']);
            $table->dropColumn('visita_id');
        });

        Schema::table('presencia_varroa', function (Blueprint $table) {
            $table->dropForeign(['visita_id']);
            $table->dropColumn('visita_id');
        });
    }
}
