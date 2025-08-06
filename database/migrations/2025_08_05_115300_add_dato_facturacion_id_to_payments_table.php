<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('dato_facturacion_id')->nullable()->after('user_id');

            $table->foreign('dato_facturacion_id')
                  ->references('id')
                  ->on('datos_facturacion')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Primero eliminamos la restricción de clave foránea
            $table->dropForeign(['dato_facturacion_id']);

            // Luego eliminamos la columna
            $table->dropColumn('dato_facturacion_id');
        });
    }
};
