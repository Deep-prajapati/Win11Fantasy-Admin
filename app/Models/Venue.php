<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'country_id',
        'name',
        'city',
        'image_path',
        'capacity',
        'floodlight',
    ];

    protected $casts = [
        'floodlight' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get the country associated with the venue
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get fixtures played at this venue
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * Scope a query to only include venues with floodlights
     */
    public function scopeWithFloodlights($query)
    {
        return $query->where('floodlight', true);
    }

    /**
     * Scope a query to only include venues without floodlights
     */
    public function scopeWithoutFloodlights($query)
    {
        return $query->where('floodlight', false);
    }

    /**
     * Scope a query to only include venues with capacity greater than the specified amount
     */
    public function scopeMinCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }

    /**
     * Scope a query to only include venues in a specific country
     */
    public function scopeInCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }
}
