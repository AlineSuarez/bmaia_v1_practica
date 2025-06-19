<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('desarrollo_cria', function (Blueprint $table) {
            $table->integer('cantidad_marcos_con_cria')->nullable();
            $table->integer('cantidad_marcos_con_abejas')->nullable();
            $table->integer('cantidad_reservas')->comment('miel/polén')->nullable();
            $table->boolean('presencia_zanganos')->default(false);
        });
    }

    public function down()
    {
        Schema::table('desarrollo_cria', function (Blueprint $table) {
            $table->dropColumn([
                'cantidad_marcos_con_cria',
                'cantidad_marcos_con_abejas',
                'cantidad_reservas',
                'presencia_zanganos',
            ]);
        });
    }
};
