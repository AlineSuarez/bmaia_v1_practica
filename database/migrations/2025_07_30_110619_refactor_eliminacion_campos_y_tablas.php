<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorEliminacionCamposYTablas extends Migration
{
    public function up(): void
    {
        // Eliminar columnas de movimientos_colmenas
        Schema::table('movimientos_colmenas', function (Blueprint $table) {
            if (Schema::hasColumn('movimientos_colmenas', 'observaciones')) {
                $table->dropColumn('observaciones');
            }
        });

        // Eliminar columnas de apiarios
        Schema::table('apiarios', function (Blueprint $table) {
            if (Schema::hasColumn('apiarios', 'installation_date')) {
                $table->dropColumn('installation_date');
            }
            if (Schema::hasColumn('apiarios', 'exposicion_solar')) {
                $table->dropColumn('exposicion_solar');
            }
            if (Schema::hasColumn('apiarios', 'url')) {
                $table->dropColumn('url');
            }
            if (Schema::hasColumn('apiarios', 'nombre_comuna')) {
                $table->dropColumn('nombre_comuna');
            }
        });

        // Eliminar columnas de colmenas
        Schema::table('colmenas', function (Blueprint $table) {
            if (Schema::hasColumn('colmenas', 'estado_inicial')) {
                $table->dropColumn('estado_inicial');
            }
            if (Schema::hasColumn('colmenas', 'numero_marcos')) {
                $table->dropColumn('numero_marcos');
            }
            if (Schema::hasColumn('colmenas', 'observaciones')) {
                $table->dropColumn('observaciones');
            }
            if (Schema::hasColumn('colmenas', 'historial')) {
                $table->dropColumn('historial');
            }
            if (Schema::hasColumn('colmenas', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });

        // Eliminar columnas de tareas_generales
        Schema::table('tareas_generales', function (Blueprint $table) {
            if (Schema::hasColumn('tareas_generales', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });

        // Eliminar columnas de tareas_predefinidas
        Schema::table('tareas_predefinidas', function (Blueprint $table) {
            if (Schema::hasColumn('tareas_predefinidas', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('tareas_predefinidas', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        // Eliminar columnas de subtareas
        Schema::table('subtareas', function (Blueprint $table) {
            if (Schema::hasColumn('subtareas', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });

        // Eliminar columnas de presencia_varroa
        Schema::table('presencia_varroa', function (Blueprint $table) {
            if (Schema::hasColumn('presencia_varroa', 'n_colmenas_tratadas')) {
                $table->dropColumn('n_colmenas_tratadas');
            }
        });

        // Eliminar columnas de estado_nutricional
        Schema::table('estado_nutricional', function (Blueprint $table) {
            if (Schema::hasColumn('estado_nutricional', 'n_colmenas_tratadas')) {
                $table->dropColumn('n_colmenas_tratadas');
            }
        });

        // Eliminar columnas de preparacion_invernada
        Schema::table('preparacion_invernada', function (Blueprint $table) {
            if (Schema::hasColumn('preparacion_invernada', 'control_sanitario')) {
                $table->dropColumn('control_sanitario');
            }
            if (Schema::hasColumn('preparacion_invernada', 'fusion_colmenas')) {
                $table->dropColumn('fusion_colmenas');
            }
            if (Schema::hasColumn('preparacion_invernada', 'reserva_alimento')) {
                $table->dropColumn('reserva_alimento');
            }
        });

        // Eliminar columnas de users
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'invoice_company_name',
                'invoice_rut',
                'invoice_activity',
                'invoice_address',
                'invoice_region',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Eliminar tabla sistema_expertos si existe
        if (Schema::hasTable('sistema_expertos')) {
            Schema::dropIfExists('sistema_expertos');
        }

        // Eliminar tabla tasks si existe
        if (Schema::hasTable('tasks')) {
            Schema::dropIfExists('tasks');
        }
    }

    public function down(): void
    {
        Schema::table('movimientos_colmenas', function (Blueprint $table) {
            $table->text('observaciones')->nullable();
        });

        Schema::table('apiarios', function (Blueprint $table) {
            $table->date('installation_date')->nullable();
            $table->string('exposicion_solar')->nullable();
            $table->string('url')->nullable();
            $table->string('nombre_comuna')->nullable();
        });

        Schema::table('colmenas', function (Blueprint $table) {
            $table->string('estado_inicial')->nullable();
            $table->integer('numero_marcos')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('historial')->nullable();
            $table->softDeletes(); // deleted_at
        });

        Schema::table('tareas_generales', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
        });

        Schema::table('tareas_predefinidas', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('subtareas', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
        });

        Schema::table('presencia_varroa', function (Blueprint $table) {
            $table->integer('n_colmenas_tratadas')->nullable();
        });

        Schema::table('estado_nutricional', function (Blueprint $table) {
            $table->integer('n_colmenas_tratadas')->nullable();
        });

        Schema::table('preparacion_invernada', function (Blueprint $table) {
            $table->string('control_sanitario')->nullable();
            $table->string('fusion_colmenas')->nullable();
            $table->string('reserva_alimento')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('invoice_company_name')->nullable();
            $table->string('invoice_rut')->nullable();
            $table->string('invoice_activity')->nullable();
            $table->string('invoice_address')->nullable();
            $table->string('invoice_region')->nullable();
        });

        Schema::create('sistema_expertos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apiario_id');
            $table->unsignedBigInteger('colmena_id');
            $table->timestamp('fecha');
            $table->unsignedBigInteger('desarrollo_cria_id');
            $table->unsignedBigInteger('calidad_reina_id');
            $table->unsignedBigInteger('estado_nutricional_id');
            $table->unsignedBigInteger('presencia_varroa_id');
            $table->unsignedBigInteger('presencia_nosemosis_id');
            $table->unsignedBigInteger('indice_cosecha_id');
            $table->unsignedBigInteger('preparacion_invernada_id');
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['pendiente', 'en_progreso', 'completada']);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('priority')->nullable();
            $table->timestamps();
        });
    }
}

