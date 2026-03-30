<?php

namespace App\Jobs;

use App\Models\{
    Fixture,
    League,
    Season,
    Team,
    Venue
};
use App\Services\SportsMonkService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GetFixture implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $apiservice;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->apiservice = new SportsMonkService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response =  $this->apiservice->getfixtureall();

            $isLive = function ($match, $data) 
            {
                return $match->is_completed ? $match->is_live : ($data['live'] == true && Carbon::parse($data['starting_at'])->lte(Carbon::now()));
            };

            if ($response['success']) 
            {
                foreach ($response['data'] as $data) 
                {
                    Fixture::updateOrCreate([
                        'fixture_id' => $data['id']
                    ], [
                        'league_id' => $data['league_id'],
                        'season_id' => $data['season_id'],
                        'stage_id' => $data['stage_id'],
                        'round' => $data['round'],
                        'localteam_id' => $data['localteam_id'],
                        'visitorteam_id' => $data['visitorteam_id'],
                        'starting_at' =>  Carbon::parse($data['starting_at']), //->setTimezone('Asia/Kolkata'),
                        'type' => $data['type'],
                        'live' => $data['live'] == true,
                        'status' => $data['status'],
                        'last_period' => $data['last_period'],
                        'note' => $data['note'],
                        'venue_id' => $data['venue_id'],
                        'toss_won_team_id' => $data['toss_won_team_id'],
                        'winner_team_id' => $data['winner_team_id'],
                        'draw_noresult' => $data['draw_noresult'],
                        'first_umpire_id' => $data['first_umpire_id'],
                        'second_umpire_id' => $data['second_umpire_id'],
                        'tv_umpire_id' => $data['tv_umpire_id'],
                        'referee_id' => $data['referee_id'],
                        'man_of_match_id' => $data['man_of_match_id'],
                        'man_of_series_id' => $data['man_of_series_id'],
                        'total_overs_played' => $data['total_overs_played'],
                        'elected' => $data['elected'],
                        'super_over' => $data['super_over'] == true,
                        'follow_on' => $data['follow_on'] == true,
                        'localteam_dl_data' => $data['localteam_dl_data'],
                        'visitorteam_dl_data' => $data['visitorteam_dl_data'],
                        'rpc_overs' => $data['rpc_overs'],
                        'rpc_target' => $data['rpc_target'],
                        'weather_report' => $data['weather_report'],
                        'localteam_name' => $data['localteam']['name'],
                        'localteam_code' => $data['localteam']['code'],
                        'localteam_image_path' => $data['localteam']['image_path'],
                        'visitorteam_name' => $data['visitorteam']['name'],
                        'visitorteam_code' => $data['visitorteam']['code'],
                        'visitorteam_image_path' => $data['visitorteam']['image_path'],
                        'is_live' => $isLive(Fixture::where('fixture_id', $data['id'])->first() ?? new Fixture(), $data),
                        'is_cancelled' =>  $data['status'] == 'Aban.',
                    ]);

                    if (isset($data['visitorteam'])) 
                    {
                        Team::updateOrcreate([
                            'team_id' => $data['visitorteam']['id'],
                        ], [
                            'name' => $data['visitorteam']['name'],
                            'code' => $data['visitorteam']['code'],
                            'image_path' => $data['visitorteam']['image_path'],
                            'country_id' => $data['visitorteam']['country_id'],
                            'national_team' => $data['visitorteam']['national_team'],
                        ]);
                    }

                    if (isset($data['localteam'])) 
                    {
                        Team::updateOrcreate([
                            'team_id' => $data['localteam']['id'],
                        ], [
                            'name' => $data['localteam']['name'],
                            'code' => $data['localteam']['code'],
                            'image_path' => $data['localteam']['image_path'],
                            'country_id' => $data['localteam']['country_id'],
                            'national_team' => $data['localteam']['national_team'],
                        ]);
                    }

                    if (isset($data['venue'])) 
                    {
                        Venue::updateOrcreate([
                            'venue_id' => $data['venue']['id']
                        ], [
                            'country_id' => $data['venue']['country_id'],
                            'name' => $data['venue']['name'],
                            'city' => $data['venue']['city'],
                            'image_path' => $data['venue']['image_path'],
                            'capacity' => $data['venue']['capacity'],
                            'floodlight' => $data['venue']['floodlight'] == true,
                        ]);
                    }

                    if (isset($data['league'])) 
                    {
                        League::updateOrcreate([
                            'league_id' => $data['league']['id'],
                        ], [
                            'season_id' => $data['league']['season_id'],
                            'name' => $data['league']['name'],
                            'code' => $data['league']['code'],
                            'image_path' => $data['league']['image_path'],
                            'type' => $data['league']['type']
                        ]);
                    }

                    if (isset($data['season'])) 
                    {
                        Season::updateOrcreate([
                            'season_id' => $data['season']['id'],
                        ], [
                            'league_id' => $data['season']['league_id'],
                            'name' => $data['season']['name'],
                            'code' => $data['season']['code'],
                        ]);
                    }
                }
                
                Log::info([
                    'Job' => 'GetFixture',
                    'Message' => 'All data fatched',
                ]);
            } else {
                Log::info([
                    'Job' => 'GetFixture',
                    'Message' => 'Failed to fatch data',
                ]);
            }
        } catch (\Throwable $th) {
            Log::info([
                'Job' => 'GetFixture',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}