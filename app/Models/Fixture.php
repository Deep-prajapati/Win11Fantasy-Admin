<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fixture extends Model
{
    use HasFactory, Compoships;
    protected $fillable = [
        'fixture_id',
        'league_id',
        'season_id',
        'stage_id',
        'round',
        'localteam_id',
        'visitorteam_id',
        'starting_at',
        'type',
        'live',
        'status',
        'last_period',
        'note',
        'venue_id',
        'toss_won_team_id',
        'winner_team_id',
        'draw_noresult',
        'first_umpire_id',
        'second_umpire_id',
        'tv_umpire_id',
        'referee_id',
        'man_of_match_id',
        'man_of_series_id',
        'total_overs_played',
        'elected',
        'super_over',
        'follow_on',
        'localteam_dl_data',
        'visitorteam_dl_data',
        'rpc_overs',
        'rpc_target',
        'weather_report',
        'localteam_name',
        'localteam_code',
        'localteam_image_path',
        'visitorteam_name',
        'visitorteam_code',
        'visitorteam_image_path',
        'is_live',
        'is_cancelled',
        'is_completed',
        'is_prize_refund',
        'is_prize_distributed'
    ];

    protected $hidden = [
        'is_prize_refund',
        'is_prize_distributed'
    ];
    protected $casts = [
        'is_prize_refund' => 'boolean',
        'is_prize_distributed' => 'boolean',
        'is_live' => 'boolean',
        'is_cancelled' => 'boolean',
        'is_completed' => 'boolean',
        'live' => 'boolean',
        'super_over' => 'boolean',
        'follow_on' => 'boolean',
        'localteam_dl_data' => 'array',
        'visitorteam_dl_data' => 'array',
        'weather_report' => 'array',
        'starting_at' => 'datetime',
    ];

    protected $appends = ['lineup', 'mega', 'playing11', 'is_upcomming'];


    public function getIsUpcommingAttribute()
    {
        if ($this->is_live == 1) {
            return false;
        }
        return ($this->is_completed == 0 && $this->is_cancelled  == 0);
    }
    public function getLineupAttribute()
    {
        if (($this->is_live  == true || $this->is_completed == true || $this->is_cancelled  == true)) {
            return false;
        }
        return Playing11::where('fixture_id', $this->fixture_id)->count() > 0;// 22;
    }
    public function getPlaying11Attribute()
    {
        return Playing11::where('fixture_id', $this->fixture_id)->pluck('player_id');
    }
    public function getMegaAttribute()
    {
        return Contest::where('match_id', $this->fixture_id)->orderby('total_winning_prize', 'desc')->first()?->total_winning_prize ?? 1;
    }


    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class, 'league_id', 'league_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id', 'season_id');
    }
    public function teama()
    {
        return $this->hasMany(Player::class, ['team_id', 'season_id'], ['localteam_id', 'season_id']);
    }
    public function teamb()
    {
        return $this->hasMany(Player::class, ['team_id', 'season_id'], ['visitorteam_id', 'season_id']);
    }

    public function battings(){
        return $this->hasMany(Batting::class,'fixture_id','fixture_id');
    }
    public function bowlings(){
        return $this->hasMany(Bowling::class,'fixture_id','fixture_id');
    }

    public function players($teams = null)
    {
        if ($teams == null) {
            return $this->teama()->union($this->teamb())->get();
        } else {
            return $this->teama()->whereIn('player_id', $teams)->union($this->teamb()->whereIn('player_id', $teams))->get();
        }
    }
    public function contests()
    {
        return $this->hasMany(Contest::class, 'match_id', 'fixture_id');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starting_at', '>', now())
            ->orwhere('live', false)
            ->Where('status', 'NS')
            ->orderBy('starting_at', 'asc');
    }
    public function scopeLive($query)
    {
        return $query->where('live', true)->Where('status', 'Live')->whereNull('winner_team_id');
    }
    public function scopeFinished($query)
    {
        return $query->whereNotNull('winner_team_id')
            ->orWhere('status', 'Finished');
    }
    public function scopeCancelled($query)
    {
        return $query->Where('status', 'Aban.');
    }
    protected function matchName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->localteam_name . ' vs ' . $this->visitorteam_name
        );
    }
    // public function winnerTeam(): BelongsTo
    // {
    //     return $this->belongsTo(Team::class, 'winner_team_id');
    // }
    /**
     * Get the localteam associated with the fixture
     */
    public function localTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'localteam_id', 'team_id');
    }

    // /**
    //  * Get the visitorteam associated with the fixture
    //  */
    public function visitorTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'visitorteam_id', 'team_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
    protected static function booted()
    {
        static::updated(function ($fixture) {
            if ($fixture->wasChanged('live') && !$fixture->getOriginal('live') && $fixture->live === true) {
                Log::channel('fixture')->info('Fixture became live', ['fixture_id' => $fixture->fixture_id]);
            }
        });
        // static::addGlobalScope('booleanColumns', function ($query) {
        //     $query->addSelect('is_live', 'is_cancelled', 'is_completed');
        // });
    }
}

