<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivadaColumnToSubtareasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subtareas', function (Blueprint $table) {
            if (!Schema::hasColumn('subtareas', 'archivada')) {
                $table->boolean('archivada')->default(false)->after('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subtareas', function (Blueprint $table) {
            if (Schema::hasColumn('subtareas', 'archivada')) {
                $table->dropColumn('archivada');
            }
        });
    }
}
