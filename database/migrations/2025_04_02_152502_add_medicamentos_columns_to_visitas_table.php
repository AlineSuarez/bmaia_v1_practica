<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMedicamentosColumnsToVisitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->integer('num_colmenas_tratadas')->nullable();
            $table->string('motivo_tratamiento')->nullable();
            $table->string('nombre_comercial_medicamento')->nullable();
            $table->string('principio_activo_medicamento')->nullable();
            $table->string('periodo_resguardo')->nullable();
            $table->string('responsable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn([
                'num_colmenas_tratadas',
                'motivo_tratamiento',
                'nombre_comercial_medicamento',
                'principio_activo_medicamento',
                'periodo_resguardo',
                'responsable',
            ]);
        });
    }
}
