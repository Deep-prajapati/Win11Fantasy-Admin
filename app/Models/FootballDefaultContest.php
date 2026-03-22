<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FootballDefaultContest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contest_type_id',
        'contest_type',
        'contest_type_code',
        'max_entries',
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
    ];
    protected $hidden = [
        'bot_user'
    ];
    protected $casts = [
        'is_cloneable' => 'boolean',
        'is_free' => 'boolean',
        'cancellation' => 'boolean',
        'bonus_contest' => 'boolean',
        'prize_percentage' => 'float',
        'max_entries' => 'integer',
        'winner_percentage' => 'integer',
        'bot_user' => 'integer',
        'usable_bonus' => 'integer',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function prizeBreakup(){
        return $this->hasMany(FootballPrizeBreakup::class,'default_contest_id','id');
    }
}
