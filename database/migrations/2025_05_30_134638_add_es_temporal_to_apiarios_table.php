<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('apiarios', function (Blueprint $table) {
            $table->boolean('es_temporal')
                ->default(false)
                ->after('activo');
        });
    }

    public function down()
    {
        Schema::table('apiarios', function (Blueprint $table) {
            $table->dropColumn('es_temporal');
        });
    }

};
