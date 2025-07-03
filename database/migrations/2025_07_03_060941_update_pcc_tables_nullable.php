<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePccTablesNullable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'desarrollo_cria',
            'calidad_reina',
            'indice_cosecha',
            'preparacion_invernada',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                // colmena_id
                if (! Schema::hasColumn($table, 'colmena_id')) {
                    $t->foreignId('colmena_id')
                      ->nullable()
                      ->after('id')
                      ->constrained()
                      ->cascadeOnDelete();
                }
                // visita_id
                if (! Schema::hasColumn($table, 'visita_id')) {
                    $t->foreignId('visita_id')
                      ->nullable()
                      ->after('colmena_id')
                      ->constrained()
                      ->cascadeOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'desarrollo_cria',
            'calidad_reina',
            'indice_cosecha',
            'preparacion_invernada',
        ];

        // Hacemos el down en orden inverso
        foreach (array_reverse($tables) as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (Schema::hasColumn($table, 'visita_id')) {
                    $t->dropForeign([$t->getTable().'_visita_id_foreign'] ?? ['visita_id']);
                    $t->dropColumn('visita_id');
                }
                if (Schema::hasColumn($table, 'colmena_id')) {
                    $t->dropForeign([$t->getTable().'_colmena_id_foreign'] ?? ['colmena_id']);
                    $t->dropColumn('colmena_id');
                }
            });
        }
    }
}
