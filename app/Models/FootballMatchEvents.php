<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballMatchEvents extends Model
{
    protected $fillable = [
        'event_id',
        'match_id',
        'period_id',
        'participant_id',
        'type_id',
        'player_id',
        'related_player_id',
        'player_name',
        'related_player_name',
        'result',
        'info',
        'addition',
        'minute',
        'extra_minute',
        'injured',
        'team_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'event_id' => 'integer',
        'match_id' => 'integer',
        'period_id' => 'integer',
        'participant_id' => 'integer',
        'type_id' => 'integer',
        'player_id' => 'integer',
        'related_player_id' => 'integer',
        'minute' => 'integer',
        'extra_minute' => 'integer',
        'injured' => 'boolean',
        'team_id' => 'integer'
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'injured' => false,
    ];
}
