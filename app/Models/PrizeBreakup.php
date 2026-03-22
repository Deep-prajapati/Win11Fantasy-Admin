<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrizeBreakup extends Model
{
    use HasFactory,Compoships;

    protected $fillable = [
        'default_contest_id',
        'contest_type_id',
        'rank_from',
        'rank_upto',
        'prize_amount',
    ];

    protected $hidden = [
        'default_contest_id',
        'contest_type_id',
        'created_at',
        'updated_at',
        'id'
    ];
    protected $casts = [
        'prize_amount' => 'float',
        'rank_from' => 'integer',
        'rank_upto' => 'integer',
    ];
}
