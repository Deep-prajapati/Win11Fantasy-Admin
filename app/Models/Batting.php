<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Batting extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixture_id',
        'team_id',
        'player_id',
        'scoreboard',
        'sort',
        'active',
        'wicket_id',
        'ball',
        'score_id',
        'score',
        'four_x',
        'six_x',
        'catch_stump_player_id',
        'runout_by_id',
        'batsmanout_id',
        'bowling_player_id',
        'fow_score',
        'fow_balls',
        'rate',
    ];
}
