<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FloraSpecies;

class FloraSpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'common_name'     => 'Avellano',
                'scientific_name' => 'Gevuina avellana',
                'family'          => 'Proteaceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/avellano.jpg',
            ],
            [
                'common_name'     => 'Bollén',
                'scientific_name' => 'Kageneckia oblonga',
                'family'          => 'Rosaceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/bollen.jpg',
            ],
            [
                'common_name'     => 'Castaño',
                'scientific_name' => 'Castanea sativa',
                'family'          => 'Fagaceae',
                'origin'          => 'Introducida',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/castano.jpg',
            ],
            [
                'common_name'     => 'Culén',
                'scientific_name' => 'Psoralea glandulosa',
                'family'          => 'Fabaceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Arbusto',
                'image_path'      => 'flora/culen.jpg',
            ],
            [
                'common_name'     => 'Espino',
                'scientific_name' => 'Acacia caven',
                'family'          => 'Fabaceae',
                'origin'          => 'Nativa',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/espino.jpg',
            ],
            [
                'common_name'     => 'Eucalipto',
                'scientific_name' => 'Eucalyptus sp',
                'family'          => 'Myrtaceae',
                'origin'          => 'Introducida',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/eucalipto.jpg',
            ],
            [
                'common_name'     => 'Falso acacio',
                'scientific_name' => 'Robinia pseudoacacia',
                'family'          => 'Fabaceae',
                'origin'          => 'Introducida',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/falso_acacio.jpg',
            ],
            [
                'common_name'     => 'Huingán',
                'scientific_name' => 'Schinus polygamus',
                'family'          => 'Anacardiaceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/huingan.jpg',
            ],
            [
                'common_name'     => 'Litre',
                'scientific_name' => 'Lithraea caustica',
                'family'          => 'Anacardiaceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/litre.jpg',
            ],
            [
                'common_name'     => 'Maitén',
                'scientific_name' => 'Maytenus boaria',
                'family'          => 'Celastraceae',
                'origin'          => 'Nativa',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/maiten.jpg',
            ],
            [
                'common_name'     => 'Maqui',
                'scientific_name' => 'Aristotelia chilensis',
                'family'          => 'Elaeocarpaceae',
                'origin'          => 'Nativa',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Arbusto',
                'image_path'      => 'flora/maqui.jpg',
            ],
            [
                'common_name'     => 'Peumo',
                'scientific_name' => 'Cryptocarya alba',
                'family'          => 'Lauraceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/peumo.jpg',
            ],
            [
                'common_name'     => 'Peumo extranjero',
                'scientific_name' => 'Crataegus monogyna',
                'family'          => 'Rosaceae',
                'origin'          => 'Introducida',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/peumo_extranjero.jpg',
            ],
            [
                'common_name'     => 'Piñol',
                'scientific_name' => 'Lomatia dentata',
                'family'          => 'Proteaceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/pinol.jpg',
            ],
            [
                'common_name'     => 'Quillay',
                'scientific_name' => 'Quillaja saponaria',
                'family'          => 'Rosaceae',
                'origin'          => 'Endémica',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/quillay.jpg',
            ],
            [
                'common_name'     => 'Radal',
                'scientific_name' => 'Lomatia hirsuta',
                'family'          => 'Proteaceae',
                'origin'          => 'Nativa',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/radal.jpg',
            ],
            [
                'common_name'     => 'Sauce chileno',
                'scientific_name' => 'Salix chilensis',
                'family'          => 'Salicaceae',
                'origin'          => 'Nativa',
                'growth_habit'    => 'Arbóreo',
                'growth_form'     => 'Árbol',
                'image_path'      => 'flora/sauce_chileno.jpg',
            ],
        ];

        foreach ($data as $item) {
            // Para evitar duplicados si vuelves a seedear
            FloraSpecies::firstOrCreate(
                [
                    'common_name'     => $item['common_name'],
                    'scientific_name' => $item['scientific_name'],
                ],
                $item
            );
        }
    }
}
