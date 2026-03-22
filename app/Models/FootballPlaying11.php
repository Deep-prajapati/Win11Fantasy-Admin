<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballPlaying11 extends Model
{

    protected $fillable = [
        'sport_id',
        'match_id',
        'player_id',
        'team_id',
        'position_id',
        'detailed_position_id',
        'formation_field',
        'type_id',
        'jersey_number',
        'formation_position',
        'player_name',
    ];

    protected $attributes = [
        'sport_id' => 1,
        'player_id' => 0,
        'team_id' => 0,
        'position_id' => 0,
        'detailed_position_id' => 0,
        'formation_field' => '',
        'type_id' => 0,
        'jersey_number' => 0,
        'formation_position' => 0,
        'player_name' => '',
    ];

    protected static function boot()
    {
        parent::boot();

        // static::creating(function ($model) {
        //     foreach ($model->attributes as $attribute => $defaultValue) {
        //         if (!isset($model->{$attribute}) || is_null($model->{$attribute})) {
        //             $model->{$attribute} = $defaultValue;
        //         }
        //     }
        // });
    }
}
