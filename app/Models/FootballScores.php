<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FootballScores extends Model
{
    use HasFactory;
    protected $fillable = [
        'match_id',
        'participant_id',
        'type_id',
        'score',
        'participant',
        'description'
    ];
    protected $casts = [
        'score' => 'integer',
    ];
    protected $attributes = [
        'score' => 0,
        'participant' => '',
        'description' => '',
    ];
    public function getUniqueKey()
    {
        return [
            'type_id' => $this->type_id,
            'participant_id' => $this->participant_id,
            'match_id' => $this->match_id
        ];
    }
}
