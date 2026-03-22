<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballTeamPlayers extends Model
{
    protected $fillable = [
        'player_id',
        'season_id',
        'team_id',
        'transfer_id',
        'position_id',
        'detailed_position_id',
        'start',
        'end',
        'captain',
        'jersey_number',
        'position_name',
        'position_code',
        'position_developer_name',
        'position_model_type',
        'position_stat_group',
        
    ];

    protected $casts = [
        'transfer_id' => 'integer',
        'player_id' => 'integer',
        'team_id' => 'integer',
        'position_id' => 'integer',
        'detailed_position_id' => 'integer',
        'start' => 'date',
        'end' => 'date',
        'captain' => 'boolean',
        'jersey_number' => 'integer',
    ];
}
