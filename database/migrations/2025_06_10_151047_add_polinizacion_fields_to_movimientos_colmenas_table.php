<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPolinizacionFieldsToMovimientosColmenasTable extends Migration
{
    public function up()
    {
        Schema::table('movimientos_colmenas', function (Blueprint $table) {
            $table->string('cultivo')->nullable()->after('motivo_movimiento');
            $table->string('periodo_floracion')->nullable()->after('cultivo');
            $table->integer('hectareas')->nullable()->after('periodo_floracion');
        });
    }

    public function down()
    {
        Schema::table('movimientos_colmenas', function (Blueprint $table) {
            $table->dropColumn(['cultivo', 'periodo_floracion', 'hectareas']);
        });
    }
}
