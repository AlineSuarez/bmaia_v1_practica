<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDuplicatedFieldsFromVisitasTable extends Migration
{
    public function up(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn([
                'vigor_de_colmena',
                'actividad_colmena',
                'ingreso_pollen',
                'bloqueo_camara_cria',
                'presencia_celdas_reales',
                'postura_de_reina',
                'estado_de_cria',
                'postura_zanganos',
                'reserva_alimento',
                'presencia_varroa',
                'num_colmenas_tratadas',
                'nombre_comercial_medicamento',
                'principio_activo_medicamento',
                'periodo_resguardo',
                'nombres',
                'apellidos',
                'rut',
                'telefono',
                'firma',
                'num_colmenas_totales',
                'num_colmenas_activas',
                'num_colmenas_muertas',
                'num_colmenas_inspeccionadas',
                'num_colmenas_enfermas',
                'flujo_nectar_polen',
                'nombre_revisor_apiario',
                'sospecha_enfermedad',
                'observacion_primera_visita',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->string('vigor_de_colmena')->nullable();
            $table->string('actividad_colmena')->nullable();
            $table->string('ingreso_pollen')->nullable();
            $table->string('bloqueo_camara_cria')->nullable();
            $table->string('presencia_celdas_reales')->nullable();
            $table->string('postura_de_reina')->nullable();
            $table->string('estado_de_cria')->nullable();
            $table->string('postura_zanganos')->nullable();
            $table->string('reserva_alimento')->nullable();
            $table->string('presencia_varroa')->nullable();
            $table->integer('num_colmenas_tratadas')->nullable();
            $table->string('nombre_comercial_medicamento')->nullable();
            $table->string('principio_activo_medicamento')->nullable();
            $table->string('periodo_resguardo')->nullable();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('rut')->nullable();
            $table->string('telefono')->nullable();
            $table->string('firma')->nullable();
            $table->integer('num_colmenas_totales')->nullable();
            $table->integer('num_colmenas_activas')->nullable();
            $table->integer('num_colmenas_muertas')->nullable();
            $table->integer('num_colmenas_inspeccionadas')->nullable();
            $table->integer('num_colmenas_enfermas')->nullable();
            $table->string('flujo_nectar_polen')->nullable();
            $table->string('nombre_revisor_apiario')->nullable();
            $table->string('sospecha_enfermedad')->nullable();
            $table->text('observacion_primera_visita')->nullable();
        });
    }

}
