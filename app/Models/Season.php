<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends Model
{
    use HasFactory;
    protected $fillable = [
        'season_id',
        'league_id',
        'name',
        'code',
        'status',
    ];
}

