<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultContest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contest_type',
        'mrp',
        'entry_fees',
        'total_spots',
        'first_prize',
        'prize_percentage',
        'winner_percentage',
        'cancellation',
        'total_winning_prize',
        'is_free',
        'bot_user',
        'is3x',
        'extra_cash',
        'bonus_contest',
        'usable_bonus',
        'is_cloneable',
        'is_felexible',
    ];
    protected $hidden = [
        'bot_user'
    ];
    protected $casts = [
        'is_cloneable' => 'boolean',
        'is_free' => 'boolean',
        'bonus_contest' => 'boolean',
        'prize_percentage' => 'float',
        'winner_percentage' => 'integer',
        'bot_user' => 'integer',
        'usable_bonus' => 'integer',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function contestType()
    {
        return $this->hasOne(ContestType::class, 'id', 'contest_type');
    }
    public function prizeBreakup(){
        return $this->hasMany(PrizeBreakup::class,'default_contest_id','id');
    }
}
