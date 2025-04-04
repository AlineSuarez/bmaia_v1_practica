<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visitas', function (Blueprint $table) {
            // Campos para visitas generales
            $table->string('motivo')->nullable()->after('fecha_visita');
            // Inspección
            $table->integer('num_colmenas_activas')->nullable();
            $table->integer('num_colmenas_muertas')->nullable();
            $table->string('flujo_nectar_polen')->nullable();
            $table->string('nombre_revisor_apiario')->nullable();
            $table->string('sospecha_enfermedad')->nullable();
        });
    }

    public function down()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn([
                'motivo',
                'num_colmenas_activas',
                'num_colmenas_muertas',
                'flujo_nectar_polen',
                'nombre_revisor_apiario',
                'sospecha_enfermedad',
            ]);
        });
    }
};