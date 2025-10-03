<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateEstadoEnumInSubtareasTable extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite no soporta ENUM ni MODIFY
            Schema::table('subtareas', function (Blueprint $table) {
                // lo dejas como string con default 'Pendiente'
                $table->string('estado')->default('Pendiente')->change();
            });
        } else {
            // MySQL/MariaDB → tu ENUM como lo tenías
            DB::statement("
                ALTER TABLE subtareas 
                MODIFY estado 
                ENUM('Pendiente', 'En progreso', 'Completada', 'Vencida') 
                DEFAULT 'Pendiente'
            ");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('subtareas', function (Blueprint $table) {
                $table->string('estado')->default(null)->change();
            });
        } else {
            DB::statement("
                ALTER TABLE subtareas 
                MODIFY estado 
                ENUM('Pendiente', 'En progreso', 'Completada') 
                DEFAULT 'Pendiente'
            ");
        }
    }
}
