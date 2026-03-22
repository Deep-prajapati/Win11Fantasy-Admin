<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'season_id',
        'name',
        'code',
        'image_path',
        'type',
        'status'
    ];
    protected $casts = [
        'league_id' => 'integer',
        'season_id' => 'integer',
        'status' => 'boolean',
    ];
}

