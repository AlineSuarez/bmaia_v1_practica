<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comunas', function (Blueprint $table) {
            $table->double('utm_x', 15, 6)->nullable()->after('lon');
            $table->double('utm_y', 15, 6)->nullable()->after('utm_x');
            $table->unsignedTinyInteger('utm_huso')->nullable()->after('utm_y');
        });
    }

    public function down()
    {
        Schema::table('comunas', function (Blueprint $table) {
            $table->dropColumn(['utm_x', 'utm_y', 'utm_huso']);
        });
    }

};
