<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'continent_id',
        'name',
        'image_path',
    ];

    /**
     * Get the continent associated with the country
     */
    // public function continent(): BelongsTo
    // {
    //     return $this->belongsTo(Continent::class);
    // }

    /**
     * Get the players from this country
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get the venues in this country
     */
    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    /**
     * Get teams associated with this country
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Scope a query to only include countries from a specific continent
     */
    // public function scopeFromContinent($query, $continentId)
    // {
    //     return $query->where('continent_id', $continentId);
    // }
}
