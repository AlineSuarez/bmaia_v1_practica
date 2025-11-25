<?php

namespace Database\Seeders;

use App\Models\Flor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FloresSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Tevo', 'Azara integrifolia'],
            ['Quillay', 'Quillaja saponaria'],
            ['Avellano', 'Gevuina avellana'],
            ['Boldo', 'Peumus boldus'],
            ['Peumo', 'Cryptocarya alba'],
            ['Tiahuén', 'Pitavia punctata'],
            ['Litre', 'Lithraea caustica'],
        ];
        foreach ($items as [$n,$c]) {
            Flor::updateOrCreate(['slug'=>Str::slug($n)], [
                'nombre'=>$n,'nombre_cientifico'=>$c,'descripcion'=>null
            ]);
        }
    }
}
