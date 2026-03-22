<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballPlayers extends Model
{
    protected $fillable = [
        'player_id',
        'sport_id',
        'country_id',
        'nationality_id',
        'city_id',
        'position_id',
        'detailed_position_id',
        'type_id',
        'common_name',
        'firstname',
        'lastname',
        'name',
        'display_name',
        'image_path',
        'height',
        'weight',
        'date_of_birth',
        'gender',
    ];

    protected $casts = [
        'player_id' => 'integer',
        'sport_id' => 'integer',
        'country_id' => 'integer',
        'nationality_id' => 'integer',
        'position_id' => 'integer',
        'detailed_position_id' => 'integer',
        'type_id' => 'integer',
        'height' => 'integer',
        'weight' => 'integer',
        'date_of_birth' => 'date',
    ];
}
