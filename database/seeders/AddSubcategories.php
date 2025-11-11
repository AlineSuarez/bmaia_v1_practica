<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSubcategories extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $subcategories = [
            [
                'id'=>1,
                'nombreSubcategoria'=>'Control de Varroa'
            ],
            [
                'id'=>2,
                'nombreSubcategoria'=>'Seguridad'
            ],
            [
                'id'=>3,
                'nombreSubcategoria'=>'Accesorio'
            ],
            [
                'id'=>4,
                'nombreSubcategoria'=>'Almacenamiento'
            ],
            [
                'id'=>5,
                'nombreSubcategoria'=>'Envasado'
            ],
            [
                'id'=>6,
                'nombreSubcategoria'=>'Desinfeccion y Limpieza'
            ],
            [
                'id'=>7,
                'nombreSubcategoria'=>'Alimentacion'
            ],
            [
                'id'=>8,
                'nombreSubcategoria'=>'Equipo de Cosecha'
            ],
            [
                'id'=>9,
                'nombreSubcategoria'=>'Control de Plagas'
            ],
        ];

        foreach ($subcategories as $subcategory) {
            DB::table('subcategories')
                ->where('id', $subcategory['id'])
                ->update([
                    'nombreSubcategoria' => $subcategory['nombreSubcategoria'],
                ]);
        }

        $existingIds = DB::table('subcategories')->pluck('id')->toArray();
        $subcategoriesToInsert = array_filter($subcategories, function($subcategory) use ($existingIds) {
            return !in_array($subcategory['id'], $existingIds);
        });

        if (!empty($subcategoriesToInsert)) {
            DB::table('subcategories')->insert($subcategoriesToInsert);
        }
    }
}
