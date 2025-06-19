<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNColmenasTratadasNullableOnPresenciaVarroaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presencia_varroa', function (Blueprint $table) {
            // Hacemos la columna nullable y le quitamos cualquier valor por defecto NOT NULL
            $table
                ->integer('n_colmenas_tratadas')
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presencia_varroa', function (Blueprint $table) {
            // Volver a forzar NOT NULL; asignamos default = 0 para no romper datos existentes
            $table
                ->integer('n_colmenas_tratadas')
                ->nullable(false)
                ->default(0)
                ->change();
        });
    }
}
