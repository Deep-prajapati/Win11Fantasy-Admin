<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FootballMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'sport_id',
        'league_id',
        'season_id',
        'stage_id',
        'round_id',
        'venue_id',
        'status',
        'result_info',
        'leg',
        'name',
        'starting_at',
        'length',
        'placeholder',
        'has_odds',
        'winning_team_id',
        'has_premium_odds',
        'starting_at_timestamp',
        'is_upcomming',
        'is_live',
        'is_completed',
        'is_cancelled',
        'is_prize_distributed',
        'is_prize_refunded',
    ];

    protected $casts = [
        'starting_at' => 'datetime',
        'is_upcomming' => 'boolean',
        'is_live' => 'boolean',
        'is_completed' => 'boolean',
        'is_cancelled' => 'boolean',
        'is_prize_distributed' => 'boolean',
        'is_prize_refunded' => 'boolean',
    ];
    protected $hidden = [
        'is_prize_refunded',
        'is_prize_distributed',
    ];
    protected $attributes = [
        'result_info' => '',
        'leg' => '',
        'status' => 'NA',
        'is_upcomming' => false,
        'is_live' => false,
        'is_completed' => false,
        'is_cancelled' => false,
        'is_prize_distributed' => false,
        'is_prize_refunded' => false,
    ];
    protected $appends = ['mega', 'lineup','playing11'];
    public function getMegaAttribute()
    {
        return FootballContest::where('match_id', $this->match_id)->orderby('total_winning_prize', 'desc')->first()?->total_winning_prize ?? 1;
    }
    public function getlineupAttribute()
    {
        return Carbon::parse($this->starting_at)->lessThan(now()->addMinutes(30)) &&
            FootballPlaying11::where('match_id', $this->match_id)->count() >= 22;
    }
    public function getPlaying11Attribute()
    {
        if($this->lineup && $this->is_upcomming){
            return FootballPlaying11::where('match_id',$this->match_id)->whereNot('formation_position',0)->pluck('player_id');
        }else{
            return [];
        }
    }
    public function scopeNeedUpdate($query)
    {
        return $query->whereDate('starting_at', '>=', now()->startOfDay())
            ->whereDate('starting_at', '<=', now()->endOfDay())
            ->orderBy('starting_at', 'asc');
    }
    public function season()
    {
        return $this->belongsTo(FootballSeason::class, 'season_id', 'season_id');
    }
    public function participants()
    {
        return $this->hasMany(FootballParticipant::class, 'match_id', 'match_id');
    }
    public function league()
    {
        return $this->belongsTo(FootballLeague::class, 'league_id', 'league_id');
    }
    public function scopeWithLeagueAndParticipants($query)
    {
        return $query->with([
            'league' => function ($q) {
                $q->select('league_id', 'name');
            },
            'participants' => function ($q) {
                $q->select('team_id', 'match_id', 'name', 'short_code', 'image_path', 'location');
            },
        ]);
    }
    
}
