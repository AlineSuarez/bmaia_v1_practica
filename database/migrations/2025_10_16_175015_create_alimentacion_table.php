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
        Schema::create('alimentacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apiario_id')->constrained('apiarios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_aplicacion');
            $table->string('tipo_alimentacion', 100); // Jarabe de azúcar, Fondant, Pasta proteica, Polen, Candy
            $table->string('objetivo', 50); // Estimulación, Mantención, Emergencia
            $table->string('insumo_utilizado', 100); // Azúcar, Miel, Polen, Proteína
            $table->string('dosificacion', 50); // 2:1, 1:1, 500g, etc.
            $table->string('metodo_utilizado', 50)->nullable(); // Bolsa, Alimentador interno, Alimentador externo
            $table->decimal('cantidad_kg', 10, 2)->nullable(); // Cantidad total aplicada
            $table->integer('num_colmenas')->default(1); // Número de colmenas alimentadas
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
        Schema::dropIfExists('alimentacion');
    }
};
