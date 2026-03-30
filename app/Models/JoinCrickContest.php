<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JoinCrickContest extends Model
{
    use HasFactory;

    protected $table = 'join_crick_contests';

    protected $fillable = [
        'match_id',
        'user_id',
        'contest_id',
        'created_contest_id',
        'created_team_id',
        'contests',
        'team_count',
        'ranks',
        'points',
        'prize_amount',
        'winning_amount',
        'entryfee_bonus',
        'entryfee_deposit',
        'entryfee_winning',
        'cancel_contest',
        'user_name',
        'contest_name',
        'is_prize_distributed',
        'is_inv_cal',
        'is_inv_cal_mon',
    ];

    protected $casts = [
        'match_id'          => 'integer',
        'user_id'           => 'integer',
        'contest_id'        => 'integer',
        'created_contest_id'   => 'integer',
        'ranks'             => 'integer',
        'points'            => 'float',
        'prize_amount'      => 'float',
        'winning_amount'    => 'float',
        'entryfee_bonus'    => 'float',
        'entryfee_deposit'  => 'float',
        'entryfee_winning'  => 'float',
        'cancel_contest'    => 'boolean',
        'is_prize_distributed' => 'boolean',
        'is_inv_cal'        => 'boolean',
        'is_inv_cal_mon'    => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function team()
    {
        return $this->belongsTo(UserTeam::class,'created_team_id','id')->withCaptionsImg();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($contest) {
            $latestcontest = static::latest('team_count')
                ->where([
                    'user_id' => $contest->user_id,
                    'match_id' => $contest->match_id,
                    'contest_id' => $contest->contest_id
                ])
                ->first();
            if ($latestcontest) {
                $counts = (int)substr($latestcontest->team_count, strlen('T'));
                $numer = $counts + 1;
            } else {
                $numer = 1;
            }
            $contest->team_count = 'T' . $numer;
        });
    }
}
