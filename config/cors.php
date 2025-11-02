<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Define los orígenes, métodos y encabezados permitidos para las peticiones
    | hacia la API. Es fundamental para permitir el acceso desde Flutter,
    | n8n, Postman o navegadores externos.
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:*',
        'http://127.0.0.1:*',
        'http://10.0.2.2:*',        // Android Emulator
        'http://10.0.3.2:*',        // Genymotion
        'https://www.bmaia.cl',     // Producción
        'https://bmaia.cl',         // Alternativa sin www
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
