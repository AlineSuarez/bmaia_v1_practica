<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('visitas', 'user_id')) {
            Schema::table('visitas', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('apiario_id')
                    ->constrained()
                    ->nullOnDelete(); // en vez de cascadeOnDelete si quieres mantener las visitas
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('visitas', 'user_id')) {
            Schema::table('visitas', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
