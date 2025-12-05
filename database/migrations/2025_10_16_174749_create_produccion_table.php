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
        Schema::create('produccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apiario_id')->constrained('apiarios')->onDelete('cascade');
            $table->foreignId('colmena_id')->nullable()->constrained('colmenas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_cosecha');
            $table->string('tipo_producto', 50)->default('miel'); // miel, polen, propóleo, jalea real, cera
            $table->decimal('cantidad_kg', 10, 2);
            $table->string('calidad', 50)->nullable(); // Extra, Primera, Segunda
            $table->decimal('humedad', 5, 2)->nullable(); // % de humedad
            $table->string('color', 50)->nullable(); // Blanco, Ámbar claro, Ámbar, Ámbar oscuro
            $table->integer('marcos_cosechados')->nullable();
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
        Schema::dropIfExists('produccion');
    }
};
