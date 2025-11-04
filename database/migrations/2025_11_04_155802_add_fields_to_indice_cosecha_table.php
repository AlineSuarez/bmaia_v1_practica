<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indice_cosecha', function (Blueprint $table) {
            // Nuevos campos complementarios para registro de cosecha
            $table->string('id_lote_cosecha')->nullable()->after('visita_id');
            $table->date('fecha_cosecha')->nullable()->after('id_lote_cosecha');
            $table->date('fecha_extraccion')->nullable()->after('fecha_cosecha');
            $table->string('lugar_extraccion')->nullable()->after('fecha_extraccion');
            $table->decimal('humedad_miel', 5, 2)->nullable()->after('lugar_extraccion');
            $table->decimal('temperatura_ambiente', 5, 2)->nullable()->after('humedad_miel');
            $table->string('responsable_cosecha')->nullable()->after('temperatura_ambiente');
            $table->text('notas')->nullable()->after('responsable_cosecha');
        });
    }

    public function down(): void
    {
        Schema::table('indice_cosecha', function (Blueprint $table) {
            $table->dropColumn([
                'id_lote_cosecha',
                'fecha_cosecha',
                'fecha_extraccion',
                'lugar_extraccion',
                'humedad_miel',
                'temperatura_ambiente',
                'responsable_cosecha',
                'notas',
            ]);
        });
    }
};
