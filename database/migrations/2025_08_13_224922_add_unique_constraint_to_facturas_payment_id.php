<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega UNIQUE a facturas.payment_id
     */
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Evita duplicados de payment_id en facturas
            $table->unique('payment_id', 'facturas_payment_id_unique');
        });
    }

    /**
     * Quita el UNIQUE de forma segura (drop FK -> drop UNIQUE -> recreate FK)
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // 1) Soltar temporalmente la FK para poder eliminar el índice UNIQUE
            //    (si no existe, Laravel ignorará silenciosamente el drop si pasamos el array de columnas)
            $table->dropForeign(['payment_id']);

            // 2) Eliminar el índice UNIQUE
            $table->dropUnique('facturas_payment_id_unique');

            // 3) Volver a crear la FK (como estaba originalmente)
            $table->foreign('payment_id')
                  ->references('id')->on('payments')
                  ->onDelete('cascade');
        });
    }
};
