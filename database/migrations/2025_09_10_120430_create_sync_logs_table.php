<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla sync_logs para idempotencia de /sync.
     */
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();

            // UUID único enviado por el móvil para cada operación
            $table->uuid('uuid')->unique();

            // Entidad afectada: apiario | colmena | visita | (agrega más si necesitas)
            $table->string('entity', 64);

            // Operación: create | update | delete
            $table->string('op', 16);

            // Usuario dueño del cambio (Sanctum/JWT)
            $table->unsignedBigInteger('user_id');

            // Momento en que se aplicó la operación en el servidor
            $table->timestamp('applied_at');

            // Campo libre para guardar datos útiles (resultado del handler, diffs, etc.)
            $table->json('meta')->nullable();

            $table->timestamps();

            // Índices de apoyo (consultas por usuario/entidad)
            $table->index(['user_id', 'entity']);
        });
    }

    /**
     * Elimina la tabla (permite migrate:rollback limpio).
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
