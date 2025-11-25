<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('region_maps', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('iso_code')->unique(); // CL-AN, CL-AT, etc.
            $table->string('slug_svg')->nullable(); // opcional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('region_maps');
    }
};
