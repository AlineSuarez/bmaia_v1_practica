<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← importa el facade DB

class MakeTratamientoNullableOnPresenciaVarroaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('presencia_varroa', function (Blueprint $table) {
            // permitimos NULL en n_colmenas_tratadas
            $table->integer('n_colmenas_tratadas')
                  ->nullable()
                  ->default(null)
                  ->change();

            // permitimos NULL en tratamiento
            $table->string('tratamiento', 255)
                  ->nullable()
                  ->default(null)
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // 1) Antes de hacer la alteración, sustituimos todos los NULL por valores válidos
        DB::table('presencia_varroa')
          ->whereNull('n_colmenas_tratadas')
          ->update(['n_colmenas_tratadas' => 0]);

        DB::table('presencia_varroa')
          ->whereNull('tratamiento')
          ->update(['tratamiento' => '']);

        // 2) Ahora podemos cambiar la definición sin que MySQL nos rechace por NULLs existentes
        Schema::table('presencia_varroa', function (Blueprint $table) {
            $table->integer('n_colmenas_tratadas')
                  ->nullable(false)
                  ->default(0)
                  ->change();

            $table->string('tratamiento', 255)
                  ->nullable(false)
                  ->default('')
                  ->change();
        });
    }
}
