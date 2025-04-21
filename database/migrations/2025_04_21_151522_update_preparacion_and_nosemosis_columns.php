<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePreparacionAndNosemosisColumns extends Migration
{
    public function up()
    {
        Schema::table('preparacion_invernada', function (Blueprint $table) {
            $table->text('control_sanitario')->nullable();
            $table->text('fusion_colmenas')->nullable();
            $table->text('reserva_alimento')->nullable();
        });

        Schema::table('presencia_nosemosis', function (Blueprint $table) {
            $table->text('signos_clinicos')->nullable();
            $table->text('muestreo_laboratorio')->nullable();
            $table->string('tratamiento')->nullable();
            $table->date('fecha_aplicacion')->nullable();
            $table->string('dosificacion')->nullable();
            $table->string('metodo_aplicacion')->nullable();
            $table->integer('num_colmenas_tratadas')->nullable();
        });
    }

    public function down()
    {
        Schema::table('preparacion_invernada', function (Blueprint $table) {
            $table->dropColumn(['control_sanitario', 'fusion_colmenas', 'reserva_alimento']);
        });

        Schema::table('presencia_nosemosis', function (Blueprint $table) {
            $table->dropColumn([
                'signos_clinicos', 'muestreo_laboratorio', 'tratamiento',
                'fecha_aplicacion', 'dosificacion', 'metodo_aplicacion', 'num_colmenas_tratadas'
            ]);
        });
    }
}
