<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estado_nutricional', function (Blueprint $table) {
            if (! Schema::hasColumn('estado_nutricional', 'colmena_id')) {
                $table->unsignedBigInteger('colmena_id')->nullable()->after('id');
            }
        });

        // agrega la FK si no existe
        try {
            DB::statement(<<<SQL
                ALTER TABLE estado_nutricional
                ADD CONSTRAINT estado_nutricional_colmena_id_foreign
                    FOREIGN KEY (colmena_id) REFERENCES colmenas(id)
                    ON DELETE CASCADE
            SQL);
        } catch (\Throwable $e) {
            // ya existe → silenciar
        }
    }

    public function down(): void
    {
        // quita la FK si existe
        try {
            DB::statement('ALTER TABLE estado_nutricional DROP FOREIGN KEY estado_nutricional_colmena_id_foreign');
        } catch (\Throwable $e) {
            // no existía → silenciar
        }

        Schema::table('estado_nutricional', function (Blueprint $table) {
            if (Schema::hasColumn('estado_nutricional', 'colmena_id')) {
                $table->dropColumn('colmena_id');
            }
        });
    }
};