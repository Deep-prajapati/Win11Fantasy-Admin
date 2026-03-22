<?php

namespace App\Models;

use Carbon\Carbon;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Player extends Model
{
    use HasFactory,Compoships;

    protected $fillable = [
        'player_id',
        'team_id',
        'country_id',
        'firstname',
        'lastname',
        'season_id',
        'fullname',
        'image_path',
        'dateofbirth',
        'gender',
        'battingstyle',
        'bowlingstyle',
        'position_id',
        'position_name',
        'credits', // new added
    ];


    protected $casts = [
        'dateofbirth' => 'date',
        'credits'=>'float',
    ];

    /**
     * Get the country associated with the player
     */
    // public function country(): BelongsTo
    // {
    //     return $this->belongsTo(Country::class);
    // }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->dateofbirth)->age;
    }

    /**
     * Scope a query to only include players from a specific country
     */
    public function scopeFromCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope a query to only include batsmen
     */
    public function scopeBatsmen($query)
    {
        return $query->where('position_id', 1);
    }

    /**
     * Scope a query to only include bowlers
     */
    public function scopeBowlers($query)
    {
        return $query->where('position_id', 2);
    }

    /**
     * Scope a query to only include all-rounders
     */
    public function scopeAllRounders($query)
    {
        return $query->where('position_id', 3);
    }

    /**
     * Scope a query to only include wicket-keepers
     */
    public function scopeWicketKeepers($query)
    {
        return $query->where('position_id', 4);
    }
}
