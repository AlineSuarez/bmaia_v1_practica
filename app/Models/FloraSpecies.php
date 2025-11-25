<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloraSpecies extends Model
{
    protected $table = 'flora_species';

    protected $fillable = [
        'common_name',
        'scientific_name',
        'family',
        'origin',
        'growth_habit',
        'nectar_type',
        'growth_form',
        'attraction_level',
        'flowering_season',
        'description',
        'habitat',
        'uses',
        'image_path',
    ];
}
