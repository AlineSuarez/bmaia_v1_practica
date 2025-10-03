<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyColmenaIdNullableInDesarrolloCria extends Migration
{
    public function up()
    {
        // ⛔️ Saltar en SQLite (testing): no hay MODIFY/CHECKS y ->change() no funciona
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        // Desactivamos temporalmente las restricciones de clave foránea para cambiar la columna
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Schema::table('desarrollo_cria', function (Blueprint $table) {
            $table->unsignedBigInteger('colmena_id')->nullable()->change();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        // Si deseas revertir y volver a NOT NULL (solo si no hay nulls en esa columna)
        Schema::table('desarrollo_cria', function (Blueprint $table) {
            $table->unsignedBigInteger('colmena_id')->nullable(false)->change();
        });
    }
}
