<?php

namespace App\Jobs;

use App\Models\CricRuns;
use App\Models\Fixture;
use App\Models\Playing11;
use App\Services\SportsMonkService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateFixture implements ShouldQueue
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
            $matches = Fixture::select('localteam_id', 'fixture_id', 'visitorteam_id', 'season_id')->addSelect('is_live', 'is_cancelled', 'is_completed')
                // ->where(function ($query) {
                //     $query->where('is_completed', false)
                //         ->orWhere('is_cancelled', false);
                // })
                ->where('is_completed', false)
                ->where('is_cancelled', false)
                ->whereDate('starting_at', '>=', Carbon::yesterday())
                ->whereDate('starting_at', '<=', Carbon::tomorrow())->get();

            foreach ($matches as $match) 
            {
                $response = $this->apiservice->getFixtureUpdates($match->fixture_id);

                if ($response['success']) 
                {
                    $data = $response['data'];
                    $isLive = function ($match, $data) 
                    {
                        return $match->is_completed ? $match->is_live : ($data['live'] == true && Carbon::parse($data['starting_at'])->lte(Carbon::now()));
                    };

                    if (count($data['lineup']) > 0) 
                    {
                        foreach ($data['lineup'] as $index => $lineup) 
                        {
                            // $team_id = ($index < 11) ? $match->localteam_id : $match->visitorteam_id;
                            $team_id = $lineup['lineup']['team_id'];
                            // $player = Player::where(['player_id' => $lineup['id'], 'team_id' => $team_id, 'season_id' => $match->season_id])->first();
                            // if (!$player) {
                            // Player::updateOrcreate([
                            //     'player_id' => $lineup['id'],
                            //     'team_id' => $team_id,
                            //     'season_id' => $match->season_id
                            // ], [
                            //     'season_id' => $match->season_id,
                            //     'country_id' => $lineup['country_id'],
                            //     'firstname' => $lineup['firstname'],
                            //     'lastname' => $lineup['lastname'],
                            //     'fullname' => $lineup['fullname'],
                            //     'image_path' => $lineup['image_path'],
                            //     'dateofbirth' => $lineup['dateofbirth'],
                            //     'gender' => $lineup['gender'],
                            //     'battingstyle' => $lineup['battingstyle'],
                            //     'bowlingstyle' => $lineup['bowlingstyle'],
                            //     'position_id' => $lineup['position']['id'],
                            //     'position_name' => $lineup['position']['name'],
                            // ]);
                            // }
                            Playing11::updateOrcreate([
                                'player_id' => $lineup['id'],
                                'fixture_id' => $match->fixture_id,
                                'team_id' => $team_id
                            ], [
                                'player_id' => $lineup['id'],
                                'fixture_id' => $match->fixture_id,
                                'team_id' => $team_id
                            ]);
                        }
                    }

                    if (count($data['runs']) > 0) 
                    {
                        $runsToUpsert = collect($data['runs'])->map(function ($runData) {
                            return [
                                'fixture_id' => $runData['fixture_id'],
                                'team_id' => $runData['team_id'],
                                'inning' => $runData['inning'] ?? '',
                                'score' => $runData['score'] ?? 0,
                                'wickets' => $runData['wickets'] ?? 0,
                                'overs' => $runData['overs'],
                                'pp1' => $runData['pp1'] ?? '',
                                'pp2' => $runData['pp2'] ?? '',
                                'pp3' => $runData['pp3'] ?? '',
                            ];
                        })->toArray();
                        CricRuns::upsert(
                            $runsToUpsert,
                            ['fixture_id', 'team_id'],
                            ['inning', 'score', 'wickets', 'overs', 'pp1', 'pp2', 'pp3']
                        );
                    }

                    Fixture::updateOrCreate([
                        'fixture_id' => $data['id']
                    ], [
                        'league_id' => $data['league_id'],
                        'season_id' => $data['season_id'],
                        'stage_id' => $data['stage_id'],
                        'round' => $data['round'],
                        'localteam_id' => $data['localteam_id'],
                        'visitorteam_id' => $data['visitorteam_id'],
                        'starting_at' =>  Carbon::parse($data['starting_at']),
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
                        // 'is_live' => ($data['live'] == true && Carbon::parse($data['starting_at'])->lte(Carbon::now())), //$data['status'] == 'Live'
                        'is_live' => $isLive(Fixture::where('fixture_id', $data['id'])->first() ?? new Fixture(), $data),
                        'is_cancelled' =>  $data['status'] == 'Aban.',
                        'is_completed' =>  $data['status'] == 'Finished',
                    ]);
                }
            }

            Log::info([
                'Job' => 'UpdateFixture',
                'Message' => 'Update fixture Successfully',
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'Job' => 'UpdateFixture',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}
