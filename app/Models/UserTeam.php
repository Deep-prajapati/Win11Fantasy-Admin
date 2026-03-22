<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'user_id',
        'name',
        'team_id',
        'caption_id',
        'voic_caption_id',
        'teams',
        'team_count',
        'team_joined_status',
        'points',
        'rank',
        'is_winning',
        'price',
        'edit_count',
        'team_create_time',
        'team_update_time'
    ];

    protected $casts = [
        'team_id' => 'array',
        'teams' => 'array',
        'team_joined_status' => 'boolean',
        'is_winning' => 'boolean',
        'points' => 'float',
        'price' => 'float',
    ];
    public function match()
    {
        return $this->belongsTo(Fixture::class, 'match_id', 'fixture_id');
    }
    public function scopeWithCaptionsImg($query)
    {
        return $query->addSelect([
            'caption_image_path' => Player::select('image_path')
                ->whereColumn('player_id', 'user_teams.caption_id')
                ->limit(1),

            'voice_caption_image_path' => Player::select('image_path')
                ->whereColumn('player_id', 'user_teams.voic_caption_id')
                ->limit(1),

            'caption_name' => Player::select('fullname')
                ->whereColumn('player_id', 'user_teams.caption_id')
                ->limit(1),

            'voice_caption_name' => Player::select('fullname')
                ->whereColumn('player_id', 'user_teams.voic_caption_id')
                ->limit(1)
        ]);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * Get teams as an array.
     */
    // public function getTeamsArrayAttribute()
    // {
    //     return explode(',', $this->teams);
    // }

    // public function setTeamsArrayAttribute($value)
    // {
    //     $this->attributes['teams'] = implode(',', $value);
    // }


    // public function caption()
    // {
    //     return $this->belongsTo(Player::class, 'caption_id');
    // }

    // public function viceCaption()
    // {
    //     return $this->belongsTo(Player::class, 'voic_caption_id');
    // }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($team) {
            $latestTeam = static::latest('team_count')
                ->where([
                    'user_id' => $team->user_id,
                    'match_id' => $team->match_id
                ])
                ->first();

            if ($latestTeam) {
                $counts = (int)substr($latestTeam->team_count, strlen('T'));
                $numer = $counts + 1;
            } else {
                $numer = 1;
            }
            $team->team_count = 'T' . $numer;
        });
    }
}
