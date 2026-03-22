<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballSeason extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'season_id',
        'league_id',
        'tie_breaker_rule_id',
        'name',
        'finished',
        'pending',
        'is_current',
        'starting_at',
        'ending_at'
    ];

    protected $casts = [
        'starting_at' => 'date',
        'ending_at' => 'date',
        'finished' => 'boolean',
        'pending' => 'boolean',
        'is_current' => 'boolean',
    ];

    public function matches()
    {
        return $this->hasMany(FootBallMatch::class, 'season_id');
    }
}
