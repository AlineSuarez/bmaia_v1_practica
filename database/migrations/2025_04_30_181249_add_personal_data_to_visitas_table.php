<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddPersonalDataToVisitasTable extends Migration
{
    public function up()
    {
        Schema::table('visitas', function (Blueprint $table) {
            // Datos del apicultor para “Visita General”
            $table->string('nombres')->nullable()->after('motivo');
            $table->string('apellidos')->nullable()->after('nombres');
            $table->string('rut')->nullable()->after('apellidos');
            $table->string('telefono')->nullable()->after('rut');
            $table->string('firma')->nullable()->after('telefono');
        });
    }

    public function down()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn(['nombres','apellidos','rut','telefono','firma']);
        });
    }
}
