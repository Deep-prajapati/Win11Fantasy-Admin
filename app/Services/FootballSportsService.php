<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class FootballSportsService
{

    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        // $this->apiKey = 'ZFGjcHonzs3SBvCCoA8DryxO3Y0q0BafBg6b1tLBfLVjaVKhE0bvIqhtEMC6';//paid key
        $this->apiKey =  SiteSettings::getValue('sportsmonk_api_key', 'i4Ajujc8Ir2PaUHTvC0OV0a9s61fAKzUHl1MJDSaeljXtRayBNS13APFbydy');
        $this->baseUrl = 'https://api.sportmonks.com/v3/football';
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
    public function getfixtures()
    {
        $startDate = Carbon::today()->subDays(10)->toDateString();
        $endDate = Carbon::today()->addDays(6)->toDateString();
       return  $this->callApi("fixtures/between/$startDate/$endDate", [
            'include' => 'league;season;participants;lineups;',
            'sort' => 'starting_at',
        ]);
    }
    public function updateMatch($fixture_id){
        return  $this->callApi("fixtures/$fixture_id", [
            'include' => 'periods;scores;participants;statistics.type;events;events.type;events.subType;events.period;events.player;events.relatedPlayer;lineups;lineups.detailedposition;aggregate;scores;scores.type;scores.participant',
            'sort' => 'starting_at'
        ]);
    }
    public function updateMatches($fixture_ids){
        return  $this->callApi("fixtures/multi/$fixture_ids", [
            'include' => 'periods;scores;participants;statistics.type;events;events.type;events.subType;events.period;events.player;events.relatedPlayer;lineups;lineups.detailedposition;aggregate;scores;scores.type;scores.participant;sidelined;sidelined.sideline;',
            'sort' => 'starting_at'
        ]);
    }
    public function geteamswithPlayes($season){
        return $this->callApi("teams/seasons/$season",[
            'include'=>"players;players.position",
        ]);
    }
    public function getPlayerInfo($playerId){
        return $this->callApi("players/$playerId");
    }

}
