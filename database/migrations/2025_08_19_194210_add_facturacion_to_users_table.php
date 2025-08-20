<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Tipo de documento elegido en el checkout
            if (!Schema::hasColumn('payments', 'doc_type')) {
                // Enum simple (boleta/factura). Si tu DB no soporta enum, cambia a string(20) y valida en app.
                $table->enum('doc_type', ['boleta', 'factura'])
                      ->default('boleta')
                      ->after('plan');
            }

            // Metadatos del comprobante (voucher interno cuando es boleta)
            if (!Schema::hasColumn('payments', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->after('doc_type');
            }
            if (!Schema::hasColumn('payments', 'receipt_issued_at')) {
                $table->dateTime('receipt_issued_at')->nullable()->after('receipt_number');
            }
            if (!Schema::hasColumn('payments', 'receipt_payment_method')) {
                $table->string('receipt_payment_method')->nullable()->after('receipt_issued_at');
            }
            if (!Schema::hasColumn('payments', 'receipt_items')) {
                $table->json('receipt_items')->nullable()->after('receipt_payment_method');
            }
            if (!Schema::hasColumn('payments', 'receipt_pdf_path')) {
                $table->string('receipt_pdf_path')->nullable()->after('receipt_items');
            }

            // (Opcionales, por si a futuro emites Boleta DTE real)
            if (!Schema::hasColumn('payments', 'boleta_folio')) {
                $table->string('boleta_folio')->nullable()->after('receipt_pdf_path');
            }
            if (!Schema::hasColumn('payments', 'sii_track_id')) {
                $table->string('sii_track_id')->nullable()->after('boleta_folio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Quitar columnas si existen (rollback seguro)
            $cols = [
                'doc_type',
                'receipt_number',
                'receipt_issued_at',
                'receipt_payment_method',
                'receipt_items',
                'receipt_pdf_path',
                'boleta_folio',
                'sii_track_id',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
