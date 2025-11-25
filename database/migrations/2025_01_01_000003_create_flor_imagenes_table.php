<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('flor_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flor_id')->constrained('flores')->cascadeOnDelete();
            $table->foreignId('fenofase_id')->constrained('fenofases')->cascadeOnDelete();
            $table->string('path');            // p.ej. flowers/tevo/plena/tevo1.jpg (en storage/app/public)
            $table->string('credito')->nullable();
            $table->boolean('es_principal')->default(true);
            $table->timestamps();

            $table->unique(['flor_id','fenofase_id','path']); // una imagen por fase, puedes tener varias si quieres
        });
    }
    public function down(): void {
        Schema::dropIfExists('flor_imagenes');
    }
};
