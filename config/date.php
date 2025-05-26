<?php
return [

    // Alias “humanos” a patrones ICU/Carbon:
    'aliases' => [
        'short'    => 'DD/MM/YYYY',
        'us'       => 'MM/DD/YYYY',
        'iso'      => 'YYYY-MM-DD',
    ],

    // Si necesitas patrones distintos por locale, puedes extender:
    'per_locale' => [
        'es' => [
            'short' => 'DD/MM/YYYY',
            'long'  => 'D [de] MMMM [de] YYYY',
        ],
        'en' => [
            'short' => 'MM/DD/YYYY',
            'long'  => 'MMMM D, YYYY',
        ],
    ],

];
