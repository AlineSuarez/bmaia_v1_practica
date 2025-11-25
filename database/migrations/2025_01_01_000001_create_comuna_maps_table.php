<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comuna_maps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('region_maps_id')
                  ->constrained('region_maps')
                  ->onDelete('cascade');

            $table->string('nombre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comuna_maps');
    }
};
