<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitasTableRebuild extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apiario_id')->constrained()->onDelete('cascade');
            $table->foreignId('colmena_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('fecha_visita');
            $table->unsignedBigInteger('desarrollo_cria_id')->nullable();
            $table->unsignedBigInteger('calidad_reina_id')->nullable();
            $table->unsignedBigInteger('presencia_varroa_id')->nullable();
            $table->unsignedBigInteger('estado_nutricional_id')->nullable();
            $table->unsignedBigInteger('indice_cosecha_id')->nullable();
            $table->unsignedBigInteger('presencia_nosemosis_id')->nullable();
            $table->unsignedBigInteger('preparacion_invernada_id')->nullable();
            $table->string('vigor_de_colmena', 100)->nullable()->comment('Nivel de vigor de la colmena');
            $table->string('actividad_colmena', 100)->nullable()->comment('Nivel de actividad de la colmena');
            $table->string('ingreso_pollen', 100)->nullable()->comment('Cantidad de polen ingresado');
            $table->string('bloqueo_camara_cria', 100)->nullable()->comment('Presencia de bloqueo en cámara de cría');
            $table->string('presencia_celdas_reales', 100)->nullable()->comment('Presencia de celdas reales');
            $table->string('postura_de_reina', 100)->nullable()->comment('Nivel de postura de la reina');
            $table->string('estado_de_cria', 100)->nullable()->comment('Estado general de la cría');
            $table->string('postura_zanganos', 100)->nullable()->comment('Nivel de postura de zánganos');
            $table->string('reserva_alimento', 100)->nullable()->comment('Nivel de reserva de alimentos');
            $table->string('presencia_varroa', 100)->nullable()->comment('Presencia de varroa en la colmena');
            $table->text('observaciones')->nullable();
            $table->string('tipo_visita')->nullable(); // Tipo de visita
            $table->integer('num_colmenas_totales')->nullable(); // Número de colmenas totales
            $table->integer('num_colmenas_inspeccionadas')->nullable(); // Número de colmenas inspeccionadas
            $table->integer('num_colmenas_enfermas')->nullable(); // Número de colmenas enfermas
            $table->text('observacion_primera_visita')->nullable(); // Columna agregada
            $table->timestamps();

            // Definir las claves foráneas
            $table->foreign('desarrollo_cria_id')->references('id')->on('desarrollo_cria')->onDelete('set null');
            $table->foreign('calidad_reina_id')->references('id')->on('calidad_reina')->onDelete('set null');
            $table->foreign('presencia_varroa_id')->references('id')->on('presencia_varroa')->onDelete('set null');
            $table->foreign('estado_nutricional_id')->references('id')->on('estado_nutricional')->onDelete('set null');
            $table->foreign('indice_cosecha_id')->references('id')->on('indice_cosecha')->onDelete('set null');
            $table->foreign('presencia_nosemosis_id')->references('id')->on('presencia_nosemosis')->onDelete('set null');
            $table->foreign('preparacion_invernada_id')->references('id')->on('preparacion_invernada')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitas');
    }
}