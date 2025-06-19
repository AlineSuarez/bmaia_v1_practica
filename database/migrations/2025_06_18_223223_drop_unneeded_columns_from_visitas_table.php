<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnneededColumnsFromVisitasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            // 1) Si la columna tiene FK, descomenta también el dropForeign correspondiente:
            // $table->dropForeign(['apiario_id']);
            // $table->dropForeign(['user_id']);
            // $table->dropForeign(['colmena_id']);
            // $table->dropForeign(['desarrollo_cria_id']);
            // $table->dropForeign(['calidad_reina_id']);
            // $table->dropForeign(['presencia_varroa_id']);
            // $table->dropForeign(['indice_cosecha_id']);
            // $table->dropForeign(['presencia_nosemosis_id']);
            // $table->dropForeign(['preparacion_invernada_id']);
            // $table->dropForeign(['estado_nutricional_id']);

            // 2) Ahora descomenta las columnas que quieras eliminar:
            // $table->dropColumn('apiario_id');
            // $table->dropColumn('user_id');
            // $table->dropColumn('colmena_id');
            // $table->dropColumn('fecha_visita');
            // $table->dropColumn('motivo');
            // $table->dropColumn('nombres');
            // $table->dropColumn('apellidos');
            // $table->dropColumn('rut');
            // $table->dropColumn('telefono');
            // $table->dropColumn('firma');
            // $table->dropColumn('desarrollo_cria_id');
            // $table->dropColumn('calidad_reina_id');
            // $table->dropColumn('presencia_varroa_id');
            // $table->dropColumn('indice_cosecha_id');
            // $table->dropColumn('presencia_nosemosis_id');
            // $table->dropColumn('preparacion_invernada_id');
            // $table->dropColumn('vigor_de_colmena');
            // $table->dropColumn('actividad_colmena');
            // $table->dropColumn('ingreso_pollen');
            // $table->dropColumn('bloqueo_camara_cria');
            // $table->dropColumn('presencia_celdas_reales');
            // $table->dropColumn('postura_de_reina');
            // $table->dropColumn('estado_de_cria');
            // $table->dropColumn('postura_zanganos');
            // $table->dropColumn('reserva_alimento');
            // $table->dropColumn('presencia_varroa');
            // $table->dropColumn('observaciones');
            // $table->dropColumn('tipo_visita');
            // $table->dropColumn('estado_nutricional_id');
            // $table->dropColumn('num_colmenas_totales');
            // $table->dropColumn('num_colmenas_inspeccionadas');
            // $table->dropColumn('num_colmenas_enfermas');
            // $table->dropColumn('observacion_primera_visita');
            // $table->dropColumn('num_colmenas_tratadas');
            // $table->dropColumn('motivo_tratamiento');
            // $table->dropColumn('nombre_comercial_medicamento');
            // $table->dropColumn('principio_activo_medicamento');
            // $table->dropColumn('periodo_resguardo');
            // $table->dropColumn('responsable');
            // $table->dropColumn('num_colmenas_activas');
            // $table->dropColumn('num_colmenas_muertas');
            // $table->dropColumn('flujo_nectar_polen');
            // $table->dropColumn('nombre_revisor_apiario');
            // $table->dropColumn('sospecha_enfermedad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            // Si necesitas revertir, descomenta y ajusta el tipo/after() según tu diseño:

            // $table->unsignedBigInteger('apiario_id')->after('id');
            // $table->unsignedBigInteger('user_id')->nullable()->after('apiario_id');
            // $table->unsignedBigInteger('colmena_id')->nullable()->after('user_id');
            // $table->date('fecha_visita')->nullable()->after('colmena_id');
            // $table->string('motivo',255)->nullable()->after('fecha_visita');
            // $table->string('nombres',255)->nullable()->after('motivo');
            // $table->string('apellidos',255)->nullable()->after('nombres');
            // $table->string('rut',255)->nullable()->after('apellidos');
            // $table->string('telefono',255)->nullable()->after('rut');
            // $table->string('firma',255)->nullable()->after('telefono');
            // $table->unsignedBigInteger('desarrollo_cria_id')->nullable()->after('firma');
            // $table->unsignedBigInteger('calidad_reina_id')->nullable()->after('desarrollo_cria_id');
            // $table->unsignedBigInteger('presencia_varroa_id')->nullable()->after('calidad_reina_id');
            // $table->unsignedBigInteger('indice_cosecha_id')->nullable()->after('presencia_varroa_id');
            // $table->unsignedBigInteger('presencia_nosemosis_id')->nullable()->after('indice_cosecha_id');
            // $table->unsignedBigInteger('preparacion_invernada_id')->nullable()->after('presencia_nosemosis_id');
            // $table->string('vigor_de_colmena',100)->nullable()->after('preparacion_invernada_id');
            // $table->string('actividad_colmena',100)->nullable()->after('vigor_de_colmena');
            // $table->string('ingreso_pollen',100)->nullable()->after('actividad_colmena');
            // $table->string('bloqueo_camara_cria',100)->nullable()->after('ingreso_pollen');
            // $table->string('presencia_celdas_reales',100)->nullable()->after('bloqueo_camara_cria');
            // $table->string('postura_de_reina',100)->nullable()->after('presencia_celdas_reales');
            // $table->string('estado_de_cria',100)->nullable()->after('postura_de_reina');
            // $table->string('postura_zanganos',100)->nullable()->after('estado_de_cria');
            // $table->string('reserva_alimento',100)->nullable()->after('postura_zanganos');
            // $table->string('presencia_varroa',100)->nullable()->after('reserva_alimento');
            // $table->text('observaciones')->nullable()->after('presencia_varroa');
            // $table->string('tipo_visita',255)->nullable()->after('observaciones');
            // $table->unsignedBigInteger('estado_nutricional_id')->nullable()->after('tipo_visita');
            // $table->integer('num_colmenas_totales')->nullable()->after('estado_nutricional_id');
            // $table->integer('num_colmenas_inspeccionadas')->nullable()->after('num_colmenas_totales');
            // $table->integer('num_colmenas_enfermas')->nullable()->after('num_colmenas_inspeccionadas');
            // $table->text('observacion_primera_visita')->nullable()->after('num_colmenas_enfermas');
            // $table->integer('num_colmenas_tratadas')->nullable()->after('observacion_primera_visita');
            // $table->string('motivo_tratamiento',255)->nullable()->after('num_colmenas_tratadas');
            // $table->string('nombre_comercial_medicamento',255)->nullable()->after('motivo_tratamiento');
            // $table->string('principio_activo_medicamento',255)->nullable()->after('nombre_comercial_medicamento');
            // $table->string('periodo_resguardo',255)->nullable()->after('principio_activo_medicamento');
            // $table->string('responsable',255)->nullable()->after('periodo_resguardo');
            // $table->integer('num_colmenas_activas')->nullable()->after('responsable');
            // $table->integer('num_colmenas_muertas')->nullable()->after('num_colmenas_activas');
            // $table->string('flujo_nectar_polen',255)->nullable()->after('num_colmenas_muertas');
            // $table->string('nombre_revisor_apiario',255)->nullable()->after('flujo_nectar_polen');
            // $table->string('sospecha_enfermedad',255)->nullable()->after('nombre_revisor_apiario');
        });
    }
}
