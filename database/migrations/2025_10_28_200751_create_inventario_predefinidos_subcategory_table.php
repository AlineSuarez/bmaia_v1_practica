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
        Schema::create('inventario_predefinido_subcategory', function (Blueprint $table) {
            $table->unsignedBigInteger('inventario_predefinido_id');
            $table->unsignedBigInteger('subcategory_id');

            $table->foreign('inventario_predefinido_id', 'inv_predef_id_fk')
                ->references('id')->on('inventario_predefinidos')
                ->onDelete('cascade');

            $table->foreign('subcategory_id', 'subcategory_id_fk')
                ->references('id')->on('subcategories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_predefinido_use_spec');
    }
};
