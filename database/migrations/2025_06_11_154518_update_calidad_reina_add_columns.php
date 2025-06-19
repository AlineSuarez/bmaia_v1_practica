<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('calidad_reina', function (Blueprint $table) {
            $table->enum('origen_reina', ['natural','comprada','fecundada','virgen'])->nullable();
            $table->string('raza')->nullable()->comment('ej. italiana, carnica, africana');
            $table->string('linea_genetica')->nullable();
            $table->date('fecha_introduccion')->nullable();
            $table->enum('estado_actual', ['activa','fallida','reemplazada'])->nullable();
            // <-- aquí cambiamos:
            $table->text('reemplazos_realizados')->nullable()->comment('texto libre, p.ej. [{"fecha":"...","motivo":"..."}]');
        });
    }


    public function down()
    {
        Schema::table('calidad_reina', function (Blueprint $table) {
            $table->dropColumn([
                'origen_reina',
                'raza',
                'linea_genetica',
                'fecha_introduccion',
                'estado_actual',
                'reemplazos_realizados',
            ]);
        });
    }
};
