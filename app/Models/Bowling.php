<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bowling extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixture_id',
        'team_id',
        'player_id',
        'scoreboard',
        'overs',
        'medians',
        'runs',
        'wickets',
        'wide',
        'noball',
        'rate',
        'active',
    ];
}
