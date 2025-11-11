<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AddProductosPredefinidos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $productos = [
            [
                'data' =>
                    [
                        'id' => 1,
                        'nombreProducto' => 'Amitraz (Apitraz)',
                        'cantidad' => 0,
                        'category_id' => 3,
                        'precio' => 0,
                        'observacion' => 'Puedes ingresar la cantidad en gramos o forma de paquetes/bolsas(unidades)',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        1,6
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 2,
                        'nombreProducto' => 'Alcohol etílico 70%',
                        'cantidad' => 0,
                        'category_id' => 3,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        6
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 3,
                        'nombreProducto' => 'Estampadora de cera',
                        'cantidad' => 0,
                        'category_id' => 4,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        8
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 4,
                        'nombreProducto' => 'Esterilizador de cera',
                        'cantidad' => 0,
                        'category_id' => 4,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        8
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 5,
                        'nombreProducto' => 'Selladora térmica manual',
                        'cantidad' => 0,
                        'category_id' => 4,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        5, 8
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 6,
                        'nombreProducto' => 'Marco alambrado',
                        'cantidad' => 0,
                        'category_id' => 5,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        3
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 7,
                        'nombreProducto' => 'Trampas de polen',
                        'cantidad' => 0,
                        'category_id' => 5,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        6
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 8,
                        'nombreProducto' => 'Azúcar flor',
                        'cantidad' => 0,
                        'category_id' => 2,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        4
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 9,
                        'nombreProducto' => 'Levadura de cerveza',
                        'cantidad' => 0,
                        'category_id' => 2,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        4
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 10,
                        'nombreProducto' => 'trampa para ratones',
                        'cantidad' => 0,
                        'category_id' => 3,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        9
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 11,
                        'nombreProducto' => 'trampa para avispas',
                        'cantidad' => 0,
                        'category_id' => 3,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        9
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 12,
                        'nombreProducto' => 'Varroa tester',
                        'cantidad' => 0,
                        'category_id' => 1,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        1
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 13,
                        'nombreProducto' => 'Cronómetro digital/analogico',
                        'cantidad' => 0,
                        'category_id' => 1,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        3
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 14,
                        'nombreProducto' => 'Sublimador de acido oxalico',
                        'cantidad' => 0,
                        'category_id' => 1,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        1
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 15,
                        'nombreProducto' => 'envase jalea real',
                        'cantidad' => 0,
                        'category_id' => 6,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        5
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 16,
                        'nombreProducto' => 'Buzo apícola',
                        'cantidad' => 0,
                        'category_id' => 7,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        2
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 17,
                        'nombreProducto' => 'Guante apícola (cuero o sintético)',
                        'cantidad' => 0,
                        'category_id' => 7,
                        'precio' => 0,
                        'observacion' => 'Puedes ingresar la cantidad en pares o paquete(unidades)',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        2
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 18,
                        'nombreProducto' => 'Botas apícola',
                        'cantidad' => 0,
                        'category_id' => 7,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        2
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 19,
                        'nombreProducto' => 'Guante de látex',
                        'cantidad' => 0,
                        'category_id' => 8,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        2
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 20,
                        'nombreProducto' => 'mascara respiratoria',
                        'cantidad' => 0,
                        'category_id' => 8,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        2
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 21,
                        'nombreProducto' => 'lamina de cera plastica',
                        'cantidad' => 0,
                        'category_id' => 9,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        3
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 22,
                        'nombreProducto' => 'Extintor portátil ABC',
                        'cantidad' => 0,
                        'category_id' => 9,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        2
                    ]
            ],
            [
                'data' =>
                    [
                        'id' => 23,
                        'nombreProducto' => 'Botiquín primeros auxilios',
                        'cantidad' => 0,
                        'category_id' => 9,
                        'precio' => 0,
                        'observacion' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                'usos' => 
                    [
                        2
                    ]
            ],
        ];

        foreach ($productos as $item) {
            DB::table('inventario_predefinidos')
                ->where('id', $item['data']['id'])
                ->update([
                    'nombreProducto' => $item['data']['nombreProducto'],
                    'cantidad' => $item['data']['cantidad'],
                    'category_id' => $item['data']['category_id'],
                    'precio' => $item['data']['precio'],
                    'observacion' => $item['data']['observacion'],
                    'created_at' => $item['data']['created_at'],
                    'updated_at' => $item['data']['updated_at'],
                ]);
        }

        $existingIds = DB::table('inventario_predefinidos')->pluck('id')->toArray();
        $itemsToInsert = array_filter($productos, function($item) use ($existingIds) {
            return !in_array($item['data']['id'], $existingIds);
        });

        if (!empty($itemsToInsert)) {
            $formattedInsert = array_map(function($item) {
                return $item['data'];
            }, $itemsToInsert);
            DB::table('inventario_predefinidos')->insert($formattedInsert);
        }

        foreach ($productos as $item) {
            foreach ($item['usos'] as $usoId) {
                DB::table('inventario_predefinido_subcategory')->updateOrInsert([
                    'inventario_predefinido_id' => $item['data']['id'],
                    'subcategory_id' => $usoId,
                ]);
            }
        }
    }
}
