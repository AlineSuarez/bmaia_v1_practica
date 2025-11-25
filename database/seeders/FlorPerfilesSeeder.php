<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlorPerfilesSeeder extends Seeder
{
    public function run(): void
    {
        // slug => perfil
        $data = [
            'avellano' => [
                'resumen'      => 'Arbusto chileno melífero; aporta néctar y polen temprano.',
                'descripcion'  => 'El avellano chileno (Gevuina avellana) florece en racimos... (texto a completar).',
                'habitat'      => 'Bosque templado; suelos bien drenados.',
                'distribucion' => 'Centro–sur de Chile.',
                'nectar_score' => 3, 'polen_score' => 4,
                'usos'         => 'Buen recurso polínico para inicios de temporada.',
                'fuente'       => 'Ficha interna',
                'enlace'       => null,
            ],
            'boldo' => [
                'resumen'      => 'Especie muy conocida; floración valiosa para abejas.',
                'descripcion'  => 'Boldo (Peumus boldus) con floración discreta pero abundante...',
                'habitat'      => 'Bosque esclerófilo.',
                'distribucion' => 'Zona central de Chile.',
                'nectar_score' => 3, 'polen_score' => 3,
                'usos'         => 'Aporta continuidad de flujo; miel de notas herbales.',
                'fuente'       => 'INIA (referencia genérica)',
                'enlace'       => null,
            ],
            'litre' => [
                'resumen'      => 'Muy melífero; precaución por dermatitis en humanos.',
                'descripcion'  => 'Lithraea caustica, conocido por producir dermatitis...',
                'habitat'      => 'Matorral y bosque esclerófilo.',
                'distribucion' => 'Zona central de Chile.',
                'nectar_score' => 4, 'polen_score' => 3,
                'usos'         => 'Fuerte aporte de néctar; planificar manejo por floración masiva.',
                'fuente'       => 'Literatura local',
                'enlace'       => null,
            ],
            'peumo' => [
                'resumen'      => 'Árbol nativo; floración atractiva para polinizadores.',
                'descripcion'  => 'Cryptocarya alba; floración extensa, buen sostén de colmenas.',
                'habitat'      => 'Bosque esclerófilo húmedo.',
                'distribucion' => 'Centro de Chile.',
                'nectar_score' => 3, 'polen_score' => 4,
                'usos'         => 'Aporta polen de buena calidad.',
                'fuente'       => 'Ficha interna',
                'enlace'       => null,
            ],
            'quillay' => [
                'resumen'      => 'Clave para miel monofloral; néctar muy abundante.',
                'descripcion'  => 'Quillaja saponaria; floración masiva y muy melífera...',
                'habitat'      => 'Laderas soleadas; suelos pobres.',
                'distribucion' => 'Zona central; plantaciones.',
                'nectar_score' => 5, 'polen_score' => 3,
                'usos'         => 'Objetivo de trashumancia por miel de quillay.',
                'fuente'       => 'INIA / experiencias locales',
                'enlace'       => null,
            ],
            'tiahue' => [
                'resumen'      => 'Especie nativa; aporte moderado.',
                'descripcion'  => 'Tiahuén (nombre local; completar especie botánica si procede)...',
                'habitat'      => 'Matorral.',
                'distribucion' => 'Centro–sur.',
                'nectar_score' => 2, 'polen_score' => 3,
                'usos'         => 'Relleno de flujo; apoya crianza.',
                'fuente'       => 'Ficha interna',
                'enlace'       => null,
            ],
            'tevo' => [
                'resumen'      => 'Aporte estable según zona; útil para sostener colmenas.',
                'descripcion'  => 'Tevo (completar nombre científico) con floración escalonada...',
                'habitat'      => 'Bosque/mb.',
                'distribucion' => 'Según región.',
                'nectar_score' => 3, 'polen_score' => 3,
                'usos'         => 'Mantención de fuerza; combinar con otras floraciones.',
                'fuente'       => 'Ficha interna',
                'enlace'       => null,
            ],
        ];

        // mapear slug -> id
        $flores = DB::table('flores')->select('id','slug')->get()->keyBy('slug');

        foreach ($data as $slug => $perfil) {
            if (!$flores->has($slug)) continue;
            $perfil['flor_id'] = $flores[$slug]->id;
            DB::table('flor_perfiles')->updateOrInsert(['flor_id'=>$perfil['flor_id']], $perfil);
        }
    }
}
