<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        // abreviatura es obligatorio según tu error
        return [
            'nombre'       => $this->faker->unique()->state(), // p.ej. "Maule"
            'abreviatura'  => strtoupper($this->faker->bothify('R##')), // p.ej. "R07"
        ];
    }

    // Estado conveniente para Maule (id=7)
    public function maule(): static
    {
        return $this->state(fn() => [
            'id'          => 7,
            'nombre'      => 'Maule',
            'abreviatura' => 'VII',
        ]);
    }
}
