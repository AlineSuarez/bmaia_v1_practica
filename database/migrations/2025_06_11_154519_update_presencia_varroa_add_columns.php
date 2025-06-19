<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('presencia_varroa', function (Blueprint $table) {
            $table->string('metodo_diagnostico')->nullable()->after('muestreo_cria_operculada')
                ->comment('alcohol, azúcar, lámina pegajosa, etc');
            $table->date('fecha_monitoreo_varroa')->nullable();
            $table->string('producto_comercial')->nullable();
            $table->string('ingrediente_activo')->nullable();
            $table->integer('periodo_carencia')->nullable()
                ->comment('n° días');
        });
    }

    public function down()
    {
        Schema::table('presencia_varroa', function (Blueprint $table) {
            $table->dropColumn([
                'metodo_diagnostico',
                'fecha_monitoreo_varroa',
                'producto_comercial',
                'ingrediente_activo',
                'periodo_carencia',
            ]);
        });
    }
};
