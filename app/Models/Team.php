<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',  // External API ID
        'name',
        'code',
        'image_path',
        'country_id',
        'national_team',
    ];

    protected $casts = [
        'national_team' => 'boolean',
    ];

    /**
     * Get fixtures where this team plays as local team
     */
    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'localteam_id', 'team_id');
    }
    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'visitorteam_id', 'team_id');
    }

    /**
     * Get all fixtures for this team (both home and away)
     */
    public function fixtures()
    {
        return Fixture::where('localteam_id', $this->team_id)
                    ->orWhere('visitorteam_id', $this->team_id);
    }
}
