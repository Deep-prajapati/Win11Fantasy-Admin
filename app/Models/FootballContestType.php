<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballContestType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'status',
    ];
    protected $casts = [
        'status' => 'boolean',
    ];
    protected $hidden = [
        'status'
    ];
}
