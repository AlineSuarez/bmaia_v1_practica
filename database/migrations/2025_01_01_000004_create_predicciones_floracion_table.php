<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('predicciones_floracion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apiario_id')->constrained('apiarios')->cascadeOnDelete();
            $table->foreignId('flor_id')->constrained('flores')->cascadeOnDelete();

            $table->date('fecha_boton')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_plena')->nullable();
            $table->date('fecha_terminal')->nullable();

            $table->year('anio')->nullable();
            $table->text('notas')->nullable();

            $table->timestamps();
            $table->unique(['apiario_id','flor_id','anio']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('predicciones_floracion');
    }
};
