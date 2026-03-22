<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FootballContest extends Model
{
    use HasFactory, SoftDeletes, Compoships;

    protected $fillable = [
        'match_id',
        'contest_type',
        'contest_type_code',
        'max_entries',
        'total_winning_prize',
        'mrp',
        'entry_fees',
        'total_spots',
        'filled_spot',
        'first_prize',
        'winner_percentage',
        'prize_percentage',
        'cancellation',
        'default_contest_id',
        'is_cancelled',
        'is_cancelable',
        'is_free',
        'is3x',
        'extra_cash',
        'is_cloned',
        'is_full',
        'sort_by',
        'usable_bonus',
        'bonus_contest',
        'auto_create',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_cancelled' => 'boolean',
        'is_cancelable' => 'boolean',
        'is_free' => 'boolean',
        'is_cloned' => 'boolean',
        'bonus_contest' => 'boolean',
        'auto_create' => 'boolean',
    ];
    public function scopeMaxEntry($query)
    {
        return $query->addSelect([
            'max_team' => ContestType::select('max_entries')
                ->whereColumn('contest_type', 'contests.contest_type')
                ->limit(1),
        ]);
    }
    public function contestType()
    {
        return $this->belongsTo(ContestType::class, 'contest_type', 'id');
    }
    public function defaultContest()
    {
        return $this->belongsTo(FootballDefaultContest::class, 'default_contest_id', 'id');
    }
    public function prizeBreakups()
    {
        return $this->hasMany(FootballPrizeBreakup::class, ['default_contest_id', 'contest_type_id'], ['default_contest_id', 'contest_type']);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    function userJoinedContests()
    {
        // return $this->hasMany(JoinCrickContest::class, 'contest_id', 'id');
    }
    public function match()
    {
        return $this->hasOne(FootballMatch::class, 'fixture_id', 'match_id');
    }
}
