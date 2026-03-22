<?php

namespace App\Services;

use App\Models\SiteSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class SportsMonkService
{

    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey =  SiteSettings::getValue('sportsmonk_api_key', 'i4Ajujc8Ir2PaUHTvC0OV0a9s61fAKzUHl1MJDSaeljXtRayBNS13APFbydy');
        $this->baseUrl = 'https://cricket.sportmonks.com/api/v2.0';
    }


    protected function callApi($endpoint, $params = [])
    {
        $params['api_token'] = $this->apiKey;
        try {
            $response = Http::timeout(60)->get("{$this->baseUrl}/{$endpoint}", $params);
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'] ?? []
                ];
            }
            return [
                'success' => false,
                'message' => 'Failed to fetch data from SportsMonk API',
                'status' => $response->status(),
                'data' => $response->json()
            ];
        } catch (RequestException $e) {
            return [
                'success' => false,
                'message' => 'Request timeout or network error',
                'error' => $e->getMessage()
            ];
        }
    }
    public function getfixtureall()
    {
        $time = now()->subDay()->toDateString();
        $to = now()->addDays(30)->toDateString();
        return $this->callApi('fixtures', [
            'filter[status]' => 'NS',
            // 'filter[league_id]' => '3,5,10', //as per plan
            'filter[starts_between]' => "$time,$to",
            'include' => 'localTeam,visitorTeam,venue,tosswon,firstumpire,firstumpire,season,league,', //lineup
            'sort' => 'starting_at'
        ]);
    }
    public function getFixtureUpdates($FIXTURE_ID)
    {
        return $this->callApi("fixtures/$FIXTURE_ID", [
            // 'filter[league_id]' => '3,5,10', //as per plan
            'include' => 'localTeam,visitorTeam,venue,tosswon,firstumpire,firstumpire,season,league,lineup,runs',
            'sort' => 'starting_at'
        ]);
    }
    public function getfixturelineup($fixture_id)
    {
        return $this->callApi("fixtures/$fixture_id", [
            'include' => 'localTeam,visitorTeam,lineup',
        ]);
    }
    public function getfixtureBettingBolling($fixture_id)
    {
        return $this->callApi("fixtures/$fixture_id", [
            'include' => 'batting,bowling',
        ]);
    }

    public function getteamsall()
    {
        return $this->callApi('teams', [
            'include' => 'squad',
            // 'sort' => 'starting_at'
        ]);
    }
    public function getTeam($team_id)
    {
        return $this->callApi("teams/$team_id", [
            'include' => 'squad',
        ]);
    }
    public function getteamSquad($team_id, $season_id)
    {
        return $this->callApi("teams/$team_id/squad/$season_id", []);
    }
}
