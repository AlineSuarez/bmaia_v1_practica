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
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apiario_id')->constrained('apiarios')->onDelete('cascade');
            $table->foreignId('colmena_id')->nullable()->constrained('colmenas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_aplicacion');
            $table->string('tipo_tratamiento', 100); // Varroa, Nosema, Loque, etc.
            $table->string('medicamento', 100);
            $table->string('nombre_comercial', 100)->nullable();
            $table->string('principio_activo', 100);
            $table->string('dosis', 50)->nullable();
            $table->integer('periodo_resguardo')->nullable(); // días
            $table->integer('num_colmenas_tratadas')->default(1);
            $table->string('responsable', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
