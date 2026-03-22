<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'match_id',
        'sport_id',
        'country_id',
        'venue_id',
        'gender',
        'name',
        'short_code',
        'image_path',
        'founded',
        'type',
        'last_played_at',
        'location',
        'position'
    ];

    protected $casts = [
        'last_played_at' => 'datetime',
    ];

    public function match()
    {
        return $this->belongsTo(FootBallMatch::class, 'match_id');
    }
}
