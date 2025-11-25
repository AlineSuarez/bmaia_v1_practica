<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fenofases', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique(); // boton, inicio, plena, terminal
            $table->string('nombre');
            $table->unsignedTinyInteger('orden')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('fenofases');
    }
};
