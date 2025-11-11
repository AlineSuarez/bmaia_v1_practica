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
        //
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventarios', 'archivada')) {
                $table->boolean('archivada')->default(false)->after('observacion');
            }
        });

        Schema::table('inventario_predefinidos', function (Blueprint $table) {
            if (!Schema::hasColumn('inventario_predefinidos', 'archivada')) {
                $table->boolean('archivada')->default(false)->after('observacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
