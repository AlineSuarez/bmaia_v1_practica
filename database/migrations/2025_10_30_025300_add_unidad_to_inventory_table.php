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
            if (!Schema::hasColumn('inventories', 'unidad')) {
                $table->string('unidad', 10)->default('Unidad')->after('cantidad');
            }
        });

        Schema::table('inventario_predefinidos', function (Blueprint $table) {
            if (!Schema::hasColumn('inventario_predefinidos', 'unidad')) {
                $table->string('unidad', 10)->default('Unidad')->after('cantidad');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            //
        });
    }
};
