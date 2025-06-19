<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('estado_nutricional', function (Blueprint $table) {
            // elimina columna
            $table->dropColumn('reserva_miel_polen');

            // añade nueva
            $table->enum('objetivo', ['estimulacion','mantencion'])->after('insumo_utilizado');
        });
    }

    public function down()
    {
        Schema::table('estado_nutricional', function (Blueprint $table) {
            // revierte
            $table->string('reserva_miel_polen')->nullable()->after('id');
            $table->dropColumn('objetivo');
        });
    }

};
