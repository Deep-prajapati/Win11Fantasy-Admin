<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CricRuns extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixture_id',
        'team_id',
        'inning',
        'score',
        'wickets',
        'overs',
        'pp1',
        'pp2',
        'pp3'
    ];

    protected $attributes = [
        'pp1' => '',
        'pp2' => '',
        'pp3' => '',
    ];
    protected function casts(): array
    {
        return [
            'fixture_id' => 'integer',
            'team_id' => 'integer',
            'inning' => 'integer',
            'score' => 'integer',
            'wickets' => 'integer',
            'overs' => 'float',
        ];
    }
}
