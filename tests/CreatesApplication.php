<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // Ruta al bootstrap de tu aplicación
        $app = require __DIR__ . '/../bootstrap/app.php';

        // Bootstrapping del kernel de consola para el entorno de testing
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
