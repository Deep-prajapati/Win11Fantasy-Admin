<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballPlayersPoints extends Model
{
    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'points',
    ];
    protected $casts = [
        'match_id' => 'integer',
        'team_id' => 'integer',
        'player_id'  => 'integer',
        'points'  => 'integer',
    ];
}
