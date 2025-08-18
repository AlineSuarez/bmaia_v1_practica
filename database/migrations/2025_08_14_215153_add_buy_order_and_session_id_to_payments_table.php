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
        Schema::table('payments', function (Blueprint $table) {
            // Columna para guardar el número de orden único de Transbank
            $table->string('buy_order', 100)->nullable()->after('billing_snapshot')->index();

            // Columna para guardar el ID de sesión generado en el pago
            $table->string('session_id', 191)->nullable()->after('buy_order')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['buy_order', 'session_id']);
        });
    }
};
