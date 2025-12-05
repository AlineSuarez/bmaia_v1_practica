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
                        'nombreProducto' => 'Alcohol Etílico 70%',
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
                        'nombreProducto' => 'Estampadora De Cera',
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
                        'nombreProducto' => 'Esterilizador De Cera',
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
                        'nombreProducto' => 'Selladora Térmica Manual',
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
                        'nombreProducto' => 'Marco Alambrado',
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
                        'nombreProducto' => 'Trampas De Polen',
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
                        'nombreProducto' => 'Azúcar Flor',
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
                        'nombreProducto' => 'Levadura De Cerveza',
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
                        'nombreProducto' => 'Trampa Para Ratones',
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
                        'nombreProducto' => 'Trampa Para Avispas',
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
                        'nombreProducto' => 'Varroa Tester',
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
                        'nombreProducto' => 'Cronómetro Digital/Analogico',
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
                        'nombreProducto' => 'Sublimador De Acido Oxalico',
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
                        'nombreProducto' => 'Envase Jalea Real',
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
                        'nombreProducto' => 'Buzo Apícola',
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
                        'nombreProducto' => 'Guante Apícola (cuero o sintético)',
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
                        'nombreProducto' => 'Botas Apícola',
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
                        'nombreProducto' => 'Guante De Látex',
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
                        'nombreProducto' => 'Mascara Respiratoria',
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
                        'nombreProducto' => 'Lamina De Cera Plastica',
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
                        'nombreProducto' => 'Extintor Portátil ABC',
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
                        'nombreProducto' => 'Botiquín Primeros Auxilios',
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
