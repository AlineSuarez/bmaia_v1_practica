<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comuna_perfiles', function (Blueprint $table) {
            // 1) FK hacia region_perfiles (relación 1→N)
            if (!Schema::hasColumn('comuna_perfiles', 'region_perfil_id')) {
                $table->unsignedBigInteger('region_perfil_id')->nullable()->after('id');
                $table->foreign('region_perfil_id')
                      ->references('id')->on('region_perfiles')
                      ->onDelete('cascade');
            }

            // 2) Campos básicos de la comuna
            if (!Schema::hasColumn('comuna_perfiles', 'nombre')) {
                $table->string('nombre', 120)->nullable()->after('region_perfil_id');
            }
            if (!Schema::hasColumn('comuna_perfiles', 'slug')) {
                $table->string('slug', 120)->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('comuna_perfiles', 'cod_externo')) {
                $table->string('cod_externo', 60)->nullable()->after('slug');
            }

            // 3) Métricas usadas en el panel derecho (ajusta precisiones si quieres)
            if (!Schema::hasColumn('comuna_perfiles', 'bosques_nativos_ha')) {
                $table->decimal('bosques_nativos_ha', 12, 2)->nullable()->after('cod_externo');
            }
            if (!Schema::hasColumn('comuna_perfiles', 'plantaciones_ha')) {
                $table->decimal('plantaciones_ha', 12, 2)->nullable()->after('bosques_nativos_ha');
            }
            if (!Schema::hasColumn('comuna_perfiles', 'pct_bosques')) {
                $table->decimal('pct_bosques', 5, 2)->nullable()->after('plantaciones_ha');
            }
            if (!Schema::hasColumn('comuna_perfiles', 'pct_plantaciones')) {
                $table->decimal('pct_plantaciones', 5, 2)->nullable()->after('pct_bosques');
            }
            if (!Schema::hasColumn('comuna_perfiles', 'notas')) {
                $table->text('notas')->nullable()->after('pct_plantaciones');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comuna_perfiles', function (Blueprint $table) {
            // Primero drop FK, luego columna
            if (Schema::hasColumn('comuna_perfiles', 'region_perfil_id')) {
                try {
                    $table->dropForeign(['region_perfil_id']);
                } catch (\Throwable $e) {
                    // algunos drivers necesitan el nombre exacto; si falla, se ignora
                }
            }

            if (Schema::hasColumn('comuna_perfiles', 'notas'))               $table->dropColumn('notas');
            if (Schema::hasColumn('comuna_perfiles', 'pct_plantaciones'))   $table->dropColumn('pct_plantaciones');
            if (Schema::hasColumn('comuna_perfiles', 'pct_bosques'))        $table->dropColumn('pct_bosques');
            if (Schema::hasColumn('comuna_perfiles', 'plantaciones_ha'))    $table->dropColumn('plantaciones_ha');
            if (Schema::hasColumn('comuna_perfiles', 'bosques_nativos_ha')) $table->dropColumn('bosques_nativos_ha');

            if (Schema::hasColumn('comuna_perfiles', 'cod_externo')) $table->dropColumn('cod_externo');
            if (Schema::hasColumn('comuna_perfiles', 'slug'))        $table->dropColumn('slug');
            if (Schema::hasColumn('comuna_perfiles', 'nombre'))      $table->dropColumn('nombre');

            if (Schema::hasColumn('comuna_perfiles', 'region_perfil_id'))   $table->dropColumn('region_perfil_id');
        });
    }
};
