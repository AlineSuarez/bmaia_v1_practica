<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presencia_varroa', function (Blueprint $table) {
            if (! Schema::hasColumn('presencia_varroa', 'colmena_id')) {
                $table->unsignedBigInteger('colmena_id')->nullable()->after('id');
            }
        });

        try {
            DB::statement(<<<SQL
                ALTER TABLE presencia_varroa
                ADD CONSTRAINT presencia_varroa_colmena_id_foreign
                    FOREIGN KEY (colmena_id) REFERENCES colmenas(id)
                    ON DELETE CASCADE
            SQL);
        } catch (\Throwable $e) {
            // silenciar si ya existía
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE presencia_varroa DROP FOREIGN KEY presencia_varroa_colmena_id_foreign');
        } catch (\Throwable $e) {
            // silenciar si no existía
        }

        Schema::table('presencia_varroa', function (Blueprint $table) {
            if (Schema::hasColumn('presencia_varroa', 'colmena_id')) {
                $table->dropColumn('colmena_id');
            }
        });
    }
};
