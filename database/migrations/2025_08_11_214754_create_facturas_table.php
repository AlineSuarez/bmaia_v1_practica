<?php

// database/migrations/XXXX_XX_XX_XXXXXX_create_facturas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();

            $table->string('numero')->nullable();
            $table->string('folio')->nullable();
            $table->string('sii_track_id')->nullable();
            $table->enum('estado', ['emitida', 'pendiente', 'anulada', 'ajustada'])->default('emitida');

            $table->unsignedInteger('monto_neto');
            $table->unsignedInteger('monto_iva');
            $table->unsignedInteger('monto_total');
            $table->unsignedTinyInteger('porcentaje_iva')->default(19);

            $table->string('moneda', 3)->default('CLP');
            $table->dateTime('fecha_emision')->nullable();
            $table->dateTime('fecha_vencimiento')->nullable();

            $table->string('pdf_url')->nullable();
            $table->string('xml_url')->nullable();
            $table->json('datos_facturacion_snapshot')->nullable();
            $table->string('plan')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'estado']);
            $table->index(['fecha_emision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
