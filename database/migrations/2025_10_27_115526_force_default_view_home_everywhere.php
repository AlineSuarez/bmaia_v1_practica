<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Crear preferencias faltantes con 'home'
        DB::unprepared("
            INSERT INTO preferences (user_id, default_view, created_at, updated_at)
            SELECT u.id, 'home', NOW(), NOW()
            FROM users u
            LEFT JOIN preferences p ON p.user_id = u.id
            WHERE p.id IS NULL
        ");

        // 2) Forzar 'home' para TODOS los existentes (incluye NULL, vacío y cualquier otro valor)
        DB::table('preferences')->update(['default_view' => 'home']);

        // 3) Asegurar DEFAULT y NOT NULL para los nuevos
        // Ajusta el tipo/longitud si tu columna es distinta (VARCHAR(50) aquí de ejemplo)
        DB::unprepared("
            ALTER TABLE preferences
            MODIFY default_view VARCHAR(50) NOT NULL DEFAULT 'home'
        ");
    }

    public function down(): void
    {
        // Revertimos sólo la definición; los datos quedan en 'home'
        DB::unprepared("
            ALTER TABLE preferences
            MODIFY default_view VARCHAR(50) NULL DEFAULT NULL
        ");
    }
};
