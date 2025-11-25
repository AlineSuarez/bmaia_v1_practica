<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('region_perfiles', function (Blueprint $table) {
            // Campos para enlazar el SVG y mostrar en el panel derecho
            if (!Schema::hasColumn('region_perfiles', 'slug')) {
                $table->string('slug', 80)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('region_perfiles', 'nombre')) {
                $table->string('nombre', 120)->nullable()->after('slug');
            }
            if (!Schema::hasColumn('region_perfiles', 'resumen')) {
                $table->text('resumen')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('region_perfiles', 'bucket')) {
                // muy_baja|baja|moderada|alta|muy_alta|sin_info
                $table->string('bucket', 20)->nullable()->after('resumen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('region_perfiles', function (Blueprint $table) {
            // En down no podemos condicionar con hasColumn en todos los drivers,
            // pero intentamos ser seguros:
            if (Schema::hasColumn('region_perfiles', 'bucket'))  $table->dropColumn('bucket');
            if (Schema::hasColumn('region_perfiles', 'resumen')) $table->dropColumn('resumen');
            if (Schema::hasColumn('region_perfiles', 'nombre'))  $table->dropColumn('nombre');
            if (Schema::hasColumn('region_perfiles', 'slug'))    $table->dropUnique(['slug']);
            if (Schema::hasColumn('region_perfiles', 'slug'))    $table->dropColumn('slug');
        });
    }
};
