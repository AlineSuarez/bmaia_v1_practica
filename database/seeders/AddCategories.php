<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddCategories extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            [
                'id'=>1,
                'nombreCategoria'=>'Equipo y Herramientas'
            ],
            [
                'id'=>2,
                'nombreCategoria'=>'Alimentacion y Suplementos'
            ],
            [
                'id'=>3,
                'nombreCategoria'=>'Insumos Sanitarios y Tratamientos'
            ],
            [
                'id'=>4,
                'nombreCategoria'=>'Maquinaria'
            ],
            [
                'id'=>5,
                'nombreCategoria'=>'Colmena'
            ],
            [
                'id'=>6,
                'nombreCategoria'=>'Envasado'
            ],
            [
                'id'=>7,
                'nombreCategoria'=>'Indumentaria'
            ],
            [
                'id'=>8,
                'nombreCategoria'=>'Materiales'
            ],
            [
                'id'=>9,
                'nombreCategoria'=>'Insumos Varios'
            ],
        ];
        
        foreach ($categories as $category) {
            DB::table('categories')
                ->where('id', $category['id'])
                ->update([
                    'nombreCategoria' => $category['nombreCategoria'],
                ]);
        }

        $existingIds = DB::table('categories')->pluck('id')->toArray();
        $categoriesToInsert = array_filter($categories, function($category) use ($existingIds) {
            return !in_array($category['id'], $existingIds);
        });

        if (!empty($categoriesToInsert)) {
            DB::table('categories')->insert($categoriesToInsert);
        }
    }
}
