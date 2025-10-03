<?php

namespace Database\Factories;

use App\Models\Comuna;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComunaFactory extends Factory
{
    protected $model = Comuna::class;

    public function definition(): array
    {
        return [
            'nombre'    => $this->faker->unique()->city(),
            'region_id' => Region::factory(),
            'lat'       => -35.426,
            'lon'       => -71.655,
            'utm_x'     => 250000.0,
            'utm_y'     => 6240000.0,
            'utm_huso'  => 19,
        ];
    }

    public function colbun(int $regionId = 7): static
    {
        return $this->state(fn() => [
            'id'        => 430,
            'nombre'    => 'Colbún',
            'region_id' => $regionId,
            'lat'       => -35.6985,
            'lon'       => -71.4172,
            'utm_x'     => 285000.0,
            'utm_y'     => 6040000.0,
            'utm_huso'  => 19,
        ]);
    }
}
