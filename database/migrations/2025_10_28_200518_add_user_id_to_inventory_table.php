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
        Schema::table('inventories', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('user_id')->nullable()->after('observacion'); // Añadir columna user_id después de observaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Definir clave foránea
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            //
            $table->dropForeign(['user_id']); // Eliminar clave foránea
            $table->dropColumn('user_id'); // Eliminar columna
        });
    }
};
