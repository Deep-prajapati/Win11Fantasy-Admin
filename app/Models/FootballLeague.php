<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballLeague extends Model
{
    protected $fillable = [
        'league_id',
        'sport_id',
        'country_id',
        'name',
        'active',
        'short_code',
        'image_path',
        'type',
        'sub_type',
        'last_played_at',
    ];

    protected $casts = [
        'league_id' => 'integer',
        'sport_id' => 'integer',
        'country_id' => 'integer',
        'active' => 'boolean',
        'last_played_at' => 'datetime',
    ];

    protected $attributes = [
        'sport_id' => 1,
        'name' => '',
        'active' => 1,
        'short_code' => '',
        'image_path' => '',
        'type' => '',
        'sub_type' => '',
    ];
}
