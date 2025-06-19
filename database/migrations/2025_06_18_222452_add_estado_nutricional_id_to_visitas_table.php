<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            if (! Schema::hasColumn('visitas', 'estado_nutricional_id')) {
                // Añade la columna después de tipo_visita
                $table->unsignedBigInteger('estado_nutricional_id')
                      ->nullable()
                      ->after('tipo_visita');

                // Crea la FK apuntando a estado_nutricional.id
                $table->foreign('estado_nutricional_id')
                      ->references('id')
                      ->on('estado_nutricional')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            if (Schema::hasColumn('visitas', 'estado_nutricional_id')) {
                // Primero eliminamos la FK (si existe)
                $table->dropForeign(['estado_nutricional_id']);
                // Luego eliminamos la columna
                $table->dropColumn('estado_nutricional_id');
            }
        });
    }
};
