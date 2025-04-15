<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateEstadoEnumInSubtareasTable extends Migration
{
    public function up()
    {
        DB::statement("
            ALTER TABLE subtareas 
            MODIFY estado 
            ENUM('Pendiente', 'En progreso', 'Completada', 'Vencida') 
            DEFAULT 'Pendiente'
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE subtareas 
            MODIFY estado 
            ENUM('Pendiente', 'En progreso', 'Completada') 
            DEFAULT 'Pendiente'
        ");
    }
}
