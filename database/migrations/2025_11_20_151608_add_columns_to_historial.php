<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('historial_cambios', function (Blueprint $table) {
            //
            $table->boolean('comprado_en_tienda')->default(false);
            $table->string('proveedor')->nullable()->after('comprado_en_tienda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial', function (Blueprint $table) {
            //
        });
    }
};
