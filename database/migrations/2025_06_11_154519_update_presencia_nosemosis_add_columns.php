<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('presencia_nosemosis', function (Blueprint $table) {
            $table->string('metodo_diagnostico_laboratorio')->nullable()
                ->after('muestreo_laboratorio');
            $table->date('fecha_monitoreo_nosema')->nullable();
            $table->string('producto_comercial')->nullable();
            $table->string('ingrediente_activo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('presencia_nosemosis', function (Blueprint $table) {
            $table->dropColumn([
                'metodo_diagnostico_laboratorio',
                'fecha_monitoreo_nosema',
                'producto_comercial',
                'ingrediente_activo',
            ]);
        });
    }

};
