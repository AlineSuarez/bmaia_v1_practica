<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('preparacion_invernada', function (Blueprint $table) {
            $table->integer('cantidad_marcos_cubiertos_abejas')->nullable();
            $table->integer('cantidad_marcos_cubiertos_cria')->nullable();
            $table->integer('marcos_reservas_miel')->nullable();
            $table->integer('presencial_reservas_polen')->nullable();
            $table->boolean('presencia_reina')->default(true);
            $table->string('nivel_infestacion_varroa')->nullable();
            $table->text('signos_enfermedades_visibles')->nullable();
            $table->date('fecha_ultima_revision_previa_receso')->nullable();
            $table->date('fecha_cierre_temporada')->nullable();
            $table->text('alimentacion_suplementaria')->nullable()
                ->comment('detalle si corresponde');
        });
    }

    public function down()
    {
        Schema::table('preparacion_invernada', function (Blueprint $table) {
            $table->dropColumn([
                'cantidad_marcos_cubiertos_abejas',
                'cantidad_marcos_cubiertos_cria',
                'marcos_reservas_miel',
                'presencial_reservas_polen',
                'presencia_reina',
                'nivel_infestacion_varroa',
                'signos_enfermedades_visibles',
                'fecha_ultima_revision_previa_receso',
                'fecha_cierre_temporada',
                'alimentacion_suplementaria',
            ]);
        });
    }
};
