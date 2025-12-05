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
        Schema::create('boletas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();

            $table->string('numero')->nullable();
            $table->string('folio')->nullable();
            $table->enum('estado', ['emitida', 'pendiente', 'anulada', 'ajustada'])->default('emitida');

            $table->unsignedInteger('monto_neto');
            $table->unsignedInteger('monto_iva');
            $table->unsignedInteger('monto_total');
            $table->unsignedTinyInteger('porcentaje_iva')->default(19);

            $table->string('moneda', 3)->default('CLP');
            $table->dateTime('fecha_emision')->nullable();
            $table->dateTime('fecha_vencimiento')->nullable();

            $table->string('pdf_url')->nullable();
            $table->json('datos_usuario_snapshot')->nullable();
            $table->string('plan')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'estado']);
            $table->index(['fecha_emision']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletas');
    }
};
