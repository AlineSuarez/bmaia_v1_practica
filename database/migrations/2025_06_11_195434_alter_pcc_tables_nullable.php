<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Alterpcctablesnullable extends Migration
{
    public function up()
    {
        // Desarrollo de Cría
        DB::statement("
            ALTER TABLE `desarrollo_cria`
              MODIFY COLUMN `vigor_colmena` VARCHAR(255) NULL,
              MODIFY COLUMN `actividad_abejas` VARCHAR(255) NULL,
              MODIFY COLUMN `ingreso_polen` VARCHAR(255) NULL,
              MODIFY COLUMN `bloqueo_camara_cria` VARCHAR(255) NULL,
              MODIFY COLUMN `presencia_celdas_reales` VARCHAR(255) NULL,
              MODIFY COLUMN `cantidad_marcos_con_cria` INT NULL,
              MODIFY COLUMN `cantidad_marcos_con_abejas` INT NULL,
              MODIFY COLUMN `cantidad_reservas` INT NULL,
              MODIFY COLUMN `presencia_zanganos` TINYINT(1) NULL
        ");

        // Calidad de la Reina
        DB::statement("
            ALTER TABLE `calidad_reina`
              MODIFY COLUMN `postura_reina` VARCHAR(255) NULL,
              MODIFY COLUMN `estado_cria` VARCHAR(255) NULL,
              MODIFY COLUMN `postura_zanganos` VARCHAR(255) NULL,
              MODIFY COLUMN `origen_reina` ENUM('natural','comprada','fecundada','virgen') NULL,
              MODIFY COLUMN `raza` VARCHAR(255) NULL,
              MODIFY COLUMN `linea_genetica` VARCHAR(255) NULL,
              MODIFY COLUMN `fecha_introduccion` DATE NULL,
              MODIFY COLUMN `estado_actual` ENUM('activa','fallida','reemplazada') NULL,
              MODIFY COLUMN `reemplazos_realizados` JSON NULL
        ");

        // Estado Nutricional
        DB::statement("
            ALTER TABLE `estado_nutricional`
              MODIFY COLUMN `tipo_alimentacion` VARCHAR(255) NULL,
              MODIFY COLUMN `fecha_aplicacion` DATE NULL,
              MODIFY COLUMN `insumo_utilizado` VARCHAR(255) NULL,
              MODIFY COLUMN `dosifiacion` VARCHAR(255) NULL,
              MODIFY COLUMN `metodo_utilizado` VARCHAR(255) NULL,
              MODIFY COLUMN `n_colmenas_tratadas` INT NULL,
              MODIFY COLUMN `objetivo` ENUM('estimulacion','mantencion') NULL
        ");

        // Presencia de Varroa
        DB::statement("
            ALTER TABLE `presencia_varroa`
              MODIFY COLUMN `diagnostico_visual` TEXT NULL,
              MODIFY COLUMN `muestreo_abejas_adultas` TEXT NULL,
              MODIFY COLUMN `muestreo_cria_operculada` TEXT NULL,
              MODIFY COLUMN `metodo_diagnostico` VARCHAR(255) NULL,
              MODIFY COLUMN `fecha_monitoreo_varroa` DATE NULL,
              MODIFY COLUMN `producto_comercial` VARCHAR(255) NULL,
              MODIFY COLUMN `ingrediente_activo` VARCHAR(255) NULL,
              MODIFY COLUMN `fecha_aplicacion` DATE NULL,
              MODIFY COLUMN `dosificacion` VARCHAR(255) NULL,
              MODIFY COLUMN `metodo_aplicacion` VARCHAR(255) NULL,
              MODIFY COLUMN `periodo_carencia` INT NULL
        ");

        // Presencia de Nosemosis
        DB::statement("
            ALTER TABLE `presencia_nosemosis`
              MODIFY COLUMN `signos_clinicos` TEXT NULL,
              MODIFY COLUMN `muestreo_laboratorio` TEXT NULL,
              MODIFY COLUMN `metodo_diagnostico_laboratorio` VARCHAR(255) NULL,
              MODIFY COLUMN `fecha_monitoreo_nosema` DATE NULL,
              MODIFY COLUMN `producto_comercial` VARCHAR(255) NULL,
              MODIFY COLUMN `ingrediente_activo` VARCHAR(255) NULL,
              MODIFY COLUMN `fecha_aplicacion` DATE NULL,
              MODIFY COLUMN `dosificacion` VARCHAR(255) NULL,
              MODIFY COLUMN `metodo_aplicacion` VARCHAR(255) NULL,
              MODIFY COLUMN `num_colmenas_tratadas` INT NULL
        ");

        // Índice de Cosecha
        DB::statement("
            ALTER TABLE `indice_cosecha`
              MODIFY COLUMN `madurez_miel` VARCHAR(255) NULL,
              MODIFY COLUMN `num_alzadas` DECIMAL(8,2) NULL,
              MODIFY COLUMN `marcos_miel` DECIMAL(8,2) NULL
        ");

        // Preparación Invernada
        DB::statement("
            ALTER TABLE `preparacion_invernada`
              MODIFY COLUMN `control_sanitario` TEXT NULL,
              MODIFY COLUMN `fusion_colmenas` TEXT NULL,
              MODIFY COLUMN `reserva_alimento` TEXT NULL,
              MODIFY COLUMN `cantidad_marcos_cubiertos_abejas` INT NULL,
              MODIFY COLUMN `cantidad_marcos_cubiertos_cria` INT NULL,
              MODIFY COLUMN `marcos_reservas_miel` INT NULL,
              MODIFY COLUMN `presencial_reservas_polen` INT NULL,
              MODIFY COLUMN `presencia_reina` TINYINT(1) NULL,
              MODIFY COLUMN `nivel_infestacion_varroa` VARCHAR(255) NULL,
              MODIFY COLUMN `signos_enfermedades_visibles` TEXT NULL,
              MODIFY COLUMN `fecha_ultima_revision_previa_receso` DATE NULL,
              MODIFY COLUMN `fecha_cierre_temporada` DATE NULL,
              MODIFY COLUMN `alimentacion_suplementaria` TEXT NULL
        ");
    }

    public function down()
    {
        
    }
}
