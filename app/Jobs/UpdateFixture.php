<?php

namespace App\Jobs;

use App\Models\CricRuns;
use App\Models\Fixture;
use App\Models\JoinCrickContest;
use App\Models\Playing11;
use App\Models\Transection;
use App\Models\User;
use App\Models\UserWallet;
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
            $matches = Fixture::select('localteam_id', 'fixture_id', 'visitorteam_id', 'season_id')
            ->addSelect('is_live', 'is_cancelled', 'is_completed')
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
                        return $match->is_completed
                            ? $match->is_live
                            : ($data['live'] == true && Carbon::parse($data['starting_at'])->lte(Carbon::now()));
                    };

                    if (count($data['lineup']) > 0) {
                        foreach ($data['lineup'] as $index => $lineup) {
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

                    if (count($data['runs']) > 0) {
                        $runsToUpsert = collect($data['runs'])->map(function ($runData) {
                            return [
                                'fixture_id' => $runData['fixture_id'],
                                'team_id' => $runData['team_id'],
                                'inning' => $runData['inning'] ?? '',
                                'score' => $runData['score'] ?? 0,
                                'wickets' => $runData['wickets'] ?? 0,
                                'overs' => $runData['overs'] ?? 0,
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
                        // 'is_completed' =>  $data['status'] == 'Finished',
                    ]);

                    if($data['status'] == 'Aban.')
                    {
                        try {
                            $users = JoinCrickContest::whereHas('contest', function ($query) use ($match) 
                            {
                                $query->where('match_id', $match->fixture_id);
                            })->with('contest')->get()->pluck('user_id')->unique();
                            
                            foreach ($users as $user_id) 
                            {
                                $user = User::find($user_id);

                                $contests = JoinCrickContest::whereHas('contest', function ($query) use ($match) 
                                {
                                    $query->where('match_id', $match->fixture_id);
                                })->where('user_id', $user_id)->with('contest')->get();

                                foreach ($contests as $joinContest) 
                                {
                                    $contest = $joinContest->contest;
                                    
                                    $wallet = UserWallet::where('user_id', $user_id)->first();

                                    if(!$wallet) 
                                    {
                                        Log::warning('No wallet found for user_id: ' . $user_id . '. Skipping refund for this user.');
                                        continue;
                                    }

                                    $wallet->bonus += $contest->entry_fees;
                                    $wallet->save();

                                    Transection::create([
                                        'user_id' => $user->id,
                                        'type' => 1,
                                        'amount' => $contest->entry_fees,
                                        'desc' => 'Refund | ' . $match->localteam_code . ' - ' . $match->visitorteam_code,
                                    ]);
                                }
                            }

                            Log::info([
                                'status' => 'success',
                                'Job' => 'UpdateFixture',
                                'fixture_id' => $match->fixture_id,
                                'Message' => 'Refunded entry fees for cancelled match successfully',
                                'Total Users Refunded' => $users->count(),
                            ]);
                        } catch (\Throwable $th) {
                            Log::error([
                                'status' => 'error',
                                'Job' => 'UpdateFixture',
                                'fixture_id' => $match->fixture_id,
                                'Message' => 'Failed to refund entry fees for cancelled match',
                                'data' => $th->getMessage()
                            ]);
                        }
                    }

                    Log::info([
                        'status' => 'success',
                        'Job' => 'UpdateFixture',
                        'Message' => 'Update fixture Successfully',
                    ]);
                }else{
                    Log::error([
                        'status' => 'error',
                        'Job' => 'UpdateFixture',
                        'Message' => 'Failed to fatch data for fixture_id: ' . $match->fixture_id,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            Log::error([
                'status' => 'error',
                'Job' => 'UpdateFixture',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}
