<?php

namespace App\Http\Controllers\Sports;

use Carbon\Carbon;
use App\Models\Team;
use App\Models\Venue;
use App\Models\League;
use App\Models\Player;
use App\Models\Season;
use App\Models\Batting;
use App\Models\Bowling;
use App\Models\Contest;
use App\Models\Fixture;
use App\Models\Playing11;
use App\Models\Playerspoint;
use Illuminate\Http\Request;
use App\Models\DefaultContest;
use Illuminate\Support\Facades\DB;
use App\Services\SportsMonkService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CricRuns;
use App\Models\JoinCrickContest;
use App\Models\Transection;
use App\Models\User;
use App\Models\UserTeam;
use App\Models\UserWallet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SportsmonkController extends Controller
{
    protected $apiservice;

    public function __construct()
    {
        $this->apiservice = new SportsMonkService();
    }

    public function getfixture()
    {
        $response =  $this->apiservice->getfixtureall();
        $isLive = function ($match, $data) {
            return $match->is_completed
                ? $match->is_live
                : ($data['live'] == true && Carbon::parse($data['starting_at'])->lte(Carbon::now()));
        };
        if ($response['success']) {
            foreach ($response['data'] as $data) {
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
                if (isset($data['visitorteam'])) {
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
                if (isset($data['localteam'])) {
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
                if (isset($data['venue'])) {
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
                if (isset($data['league'])) {
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
                if (isset($data['season'])) {
                    Season::updateOrcreate([
                        'season_id' => $data['season']['id'],
                    ], [
                        'league_id' => $data['season']['league_id'],
                        'name' => $data['season']['name'],
                        'code' => $data['season']['code'],
                    ]);
                }
            }
            return "All data fatched";
        } else {
            return "Something went wrong.";
        }
    }

    public function updatefixture()
    {
        $matches = Fixture::select('localteam_id', 'fixture_id', 'visitorteam_id', 'season_id')
            ->addSelect('is_live', 'is_cancelled', 'is_completed')
            // ->where(function ($query) {
            //     $query->where('is_completed', false)
            //         ->orWhere('is_cancelled', false);
            // })
            ->where('is_completed', false)
            ->where('is_cancelled', false)

            ->whereDate('starting_at', '>=', Carbon::yesterday())
            ->whereDate('starting_at', '<=', Carbon::tomorrow())
            ->get();
        foreach ($matches as $match) {

            $response = $this->apiservice->getFixtureUpdates($match->fixture_id);
 if ($response['success']) {
                $data = $response['data'];
$isLive = function ($match, $data) {
                    return $match->is_completed
                        ? $match->is_live
                        : ($data['live'] == true && Carbon::parse($data['starting_at'])->lte(Carbon::now()));
                };
                if (count($data['lineup']) > 0) {
                    foreach ($data['lineup'] as $index => $lineup) {
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
                if (count($data['runs']) > 0) {
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
    }
    public function getTeams()
    {
        $matches = Fixture::select('localteam_id', 'fixture_id', 'visitorteam_id', 'season_id')
            ->whereDate('starting_at', '>=', Carbon::today())
            ->whereNot('status', 'Aban.')
            ->whereDate('starting_at', '<', Carbon::today()->addDays(4))
            ->where('is_completed', false)
            ->where('is_cancelled', false)

            // ->whereIn('season_id',[6,24,44,185,309,312,498,507,782,1058,1292,1427,1648,1657,10,104,107,110,324,450,453,525,830,1079,1349,1624,15,188,191,648,986,1145,1496,1636])
            // ->skip(5)
            // ->take(5)
            ->get();

        foreach ($matches as $match) {
            $teamIds = [$match->localteam_id, $match->visitorteam_id];
            $playersData = [];

            foreach ($teamIds as $teamId) {
                $teamDetails = $this->apiservice->getteamSquad($teamId, $match->season_id);
                if (!isset($teamDetails['data']['squad'])) {
                    continue; // Skip if no squad data
                }
                if (isset($teamDetails['data']['error'])) {
                    print_r($teamDetails['data']);
                    break; // Skip if no squad data
                }

                foreach ($teamDetails['data']['squad'] as $data) {
                    $playersData[] = [
                        'player_id' => $data['id'],
                        'team_id' => $teamId,
                        'season_id' => $match->season_id,
                        'country_id' => $data['country_id'],
                        'firstname' => $data['firstname'],
                        'lastname' => $data['lastname'],
                        'fullname' => $data['fullname'],
                        'image_path' => $data['image_path'],
                        'dateofbirth' => $data['dateofbirth'],
                        'gender' => $data['gender'],
                        'battingstyle' => $data['battingstyle'],
                        'bowlingstyle' => $data['bowlingstyle'],
                        'position_id' => $data['position']['id'],
                        'position_name' => $data['position']['name'],
                        'updated_at' => now(), // Ensure timestamps are updated
                    ];
                }
            }

            // **Bulk Insert or Update for Unique Constraint (player_id, team_id, match_id)**
            if (!empty($playersData)) {
                // return $playersData;
                DB::transaction(function () use ($playersData) {
                    Player::upsert(
                        $playersData, // Data to insert/update
                        ['player_id', 'team_id', 'season_id'], // Unique key constraints
                        ['fullname', 'image_path', 'battingstyle', 'bowlingstyle', 'position_id', 'position_name', 'updated_at'] // Columns to update if conflict occurs
                    );
                });
            }
        }

        return "All data fetched and updated successfully";
    }


    // public function getfixturelineup()
    // {
    //     // $localteamDetails = $this->apiservice->getfixturelineup(65525);
    //     $matches = Fixture::whereDate('starting_at', Carbon::today())
    //         ->whereNotIn('status', ['Aban.'])
    //         ->where('is_completed', false)
    //         ->where('is_cancelled', false)

    //         ->orderby('starting_at', 'asc')
    //         ->select('fixture_id', 'season_id')
    //         ->addSelect('is_live', 'is_cancelled', 'is_completed')
    //         ->get();
    //     foreach ($matches as $value) {
    //         $localteamDetails = $this->apiservice->getfixturelineup($value->fixture_id);
    //         if ($localteamDetails['success']) {
    //             if (isset($localteamDetails['data']['lineup'])) {
    //                 $team_a = $localteamDetails['data']['localteam']['id'];
    //                 $team_b = $localteamDetails['data']['visitorteam']['id'];
    //                 foreach ($localteamDetails['data']['lineup'] as $index => $data) {
    //                     $team_id = ($index < 11) ? $team_a : $team_b;
    //                     $player = Player::where(['player_id' => $data['id'], 'team_id' => $team_id, 'season_id' => $value->season_id])->first();
    //                     if (!$player) {
    //                         Player::updateOrcreate([
    //                             'player_id' => $data['id'],
    //                             'team_id' => $team_id,
    //                             'season_id' => $value->season_id
    //                         ], [
    //                             'country_id' => $data['country_id'],
    //                             'firstname' => $data['firstname'],
    //                             'lastname' => $data['lastname'],
    //                             'fullname' => $data['fullname'],
    //                             'image_path' => $data['image_path'],
    //                             'dateofbirth' => $data['dateofbirth'],
    //                             'gender' => $data['gender'],
    //                             'battingstyle' => $data['battingstyle'],
    //                             'bowlingstyle' => $data['bowlingstyle'],
    //                             'position_id' => $data['position']['id'],
    //                             'position_name' => $data['position']['name'],
    //                         ]);
    //                     }
    //                     Playing11::updateOrcreate([
    //                         'player_id' => $data['id'],
    //                         'fixture_id' => $localteamDetails['data']['id'],
    //                         'team_id' => $team_id
    //                     ], [
    //                         'player_id' => $data['id'],
    //                         'fixture_id' => $localteamDetails['data']['id'],
    //                         'team_id' => $team_id
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    //     return "All data fatched";
    // }


    public function getBettingBolling()
    {
        // $matches = Fixture::where('is_live', true)->whereDate('starting_at', Carbon::now())->orderby('starting_at', 'asc')->pluck('fixture_id');
        // $matches = Fixture::where('fixture_id', 62242)->orderby('starting_at', 'asc')->pluck('fixture_id');
        // $details = $this->apiservice->getfixtureBettingBolling(65320);
        $matches = Fixture::where('is_live', true)->orderby('starting_at', 'asc')->pluck('fixture_id');
        foreach ($matches as $data) {
            $details = $this->apiservice->getfixtureBettingBolling($data);
            if ($details['success']) {
                $keysToRemove = ["updated_at", "resource", "id"];
                $filteredBattingData = array_map(function ($item) use ($keysToRemove) {
                    return array_diff_key($item, array_flip($keysToRemove));
                }, $details['data']['batting']);

                // foreach ($filteredBattingData as $battingItem) {
                //     Batting::updateOrCreate(
                //         [
                //             'fixture_id' => $battingItem['fixture_id'],
                //             'team_id' => $battingItem['team_id'],
                //             'player_id' => $battingItem['player_id'],
                //             'scoreboard' => $battingItem['scoreboard']
                //         ],
                //         array_diff_key($battingItem, array_flip(['fixture_id', 'team_id', 'player_id', 'scoreboard']))
                //     );
                // }
                $keysToRemove2 = ["updated_at", "resource", "id", 'sort'];
                $filteredBollingData = array_map(function ($item) use ($keysToRemove2) {
                    return array_diff_key($item, array_flip($keysToRemove2));
                }, $details['data']['bowling']);
                DB::transaction(function () use ($filteredBattingData, $filteredBollingData) {
                    // Update batting records
                    foreach ($filteredBattingData as $battingItem) {
                        Batting::updateOrCreate(
                            [
                                'fixture_id' => $battingItem['fixture_id'],
                                'team_id' => $battingItem['team_id'],
                                'player_id' => $battingItem['player_id'],
                                'scoreboard' => $battingItem['scoreboard']
                            ],
                            array_diff_key($battingItem, ['fixture_id', 'team_id', 'player_id', 'scoreboard'])
                        );
                    }

                    foreach ($filteredBollingData as $bowlingItem) {
                        Bowling::updateOrCreate(
                            [
                                'fixture_id' => $bowlingItem['fixture_id'],
                                'team_id' => $bowlingItem['team_id'],
                                'player_id' => $bowlingItem['player_id'],
                                'scoreboard' => $bowlingItem['scoreboard']
                            ],
                            array_diff_key($bowlingItem, ['fixture_id', 'team_id', 'player_id', 'scoreboard'])
                        );
                    }
                });
            }
        }
        return 'bolling and betting records updated on ' . Carbon::now();
    }

    public function generatePoints()
    {
        // $livematchs =  Fixture::where('is_live', true)->whereDate('starting_at', Carbon::now())->select('fixture_id', 'localteam_id', 'visitorteam_id')->get();
        $livematchs =  Fixture::where('is_live', true)->select('fixture_id', 'localteam_id', 'visitorteam_id')->get();
        // $livematchs = Fixture::where('fixture_id', 62242)->orderby('starting_at', 'asc')->select('fixture_id', 'localteam_id', 'visitorteam_id')->get();
        foreach ($livematchs as $key => $match) {
            if (count($match->playing11) > 0) {
                // $pointsbat = $this->battingPoints($match->fixture_id, $players);
                //    return $pointsbowl = $this->bowlingPoints($match->fixture_id, $players);
                $points = $this->getFantasyPoints($match->fixture_id, $match->playing11);

                DB::transaction(function () use ($points, $match) {
                    foreach ($points as $point) {
                        Playerspoint::updateOrCreate([
                            'team_id' => $point->team_id,
                            'player_id' => $point->player_id,
                            'fixture_id' => $match->fixture_id,
                        ], [
                            'points' => $point->total_points,
                        ]);
                    }
                });
            }
        }
        return 'Points generated';
    }
    protected function battingPoints($fixture_id, $players)
    {
        $bonus_for_four = 4;
        $bonus_for_six = 6;
        $bonus_25 = 4;
        $bonus_50 = 8;
        $bonus_100 = 16;
        return Batting::whereIn('player_id', $players)
            ->where('fixture_id', $fixture_id)
            ->selectRaw(
                'player_id, team_id,
                ROUND(
                SUM(score) +
                SUM(four_x) * ? +
                SUM(six_x) * ? +
                CASE
                    WHEN SUM(score) >= 100 THEN ?
                    WHEN SUM(score) >= 50 THEN ?
                    WHEN SUM(score) >= 25 THEN ?
                    ELSE 0
                END
            , 2) as total_points',
                [$bonus_for_four, $bonus_for_six, $bonus_100, $bonus_50, $bonus_25]
            )
            ->groupBy('player_id', 'team_id')
            ->get();
    }
    protected function bowlingPoints($fixture_id, $players)
    {
        $bonus_for_four = 4;
        $bonus_for_six = 6;
        $bonus_25 = 4;
        $bonus_50 = 8;
        $bonus_100 = 16;
        $empty = 12;
        $w = 25;
        $w_2 = 4;
        $w_3 = 8;
        $w_5 = 12;
        return Bowling::whereIn('player_id', $players)
            ->where('fixture_id', $fixture_id)
            ->selectRaw(
                'player_id, team_id,
                ROUND(
                SUM(wickets) * ? +
                SUM(medians) * ? +
                CASE
                    WHEN SUM(wickets) >= 2 THEN ?
                    WHEN SUM(wickets) >= 3 THEN ?
                    WHEN SUM(wickets) >= 5 THEN ?
                    ELSE 0
                END
            , 2) as total_points',
                [$w, $empty, $w_2, $w_3, $w_5]
            )
            ->groupBy('player_id', 'team_id')
            ->get();
    }
    protected function getFantasyPoints($fixture_id, $players)
    {
        $battingBonusForFour = 4;
        $battingBonusForSix = 6;
        $battingBonus25 = 4;
        $battingBonus50 = 8;
        $battingBonus100 = 16;

        $bowlingWicket = 25;
        $bowlingMaiden = 12;
        $bowlingWicket2 = 4;
        $bowlingWicket3 = 8;
        $bowlingWicket5 = 12;

        // Optimized single query with LEFT JOIN
        $fantasyPoints = DB::table('players')
            ->leftJoin('battings', function ($join) use ($fixture_id) {
                $join->on('players.player_id', '=', 'battings.player_id')
                    ->where('battings.fixture_id', '=', $fixture_id);
            })
            ->leftJoin('bowlings', function ($join) use ($fixture_id) {
                $join->on('players.player_id', '=', 'bowlings.player_id')
                    ->where('bowlings.fixture_id', '=', $fixture_id);
            })
            ->whereIn('players.player_id', $players)
            ->selectRaw(
                'players.player_id, players.team_id,
                ROUND(
                    COALESCE(SUM(battings.score), 0) +
                    COALESCE(SUM(battings.four_x) * ?, 0) +
                    COALESCE(SUM(battings.six_x) * ?, 0) +
                    CASE
                        WHEN COALESCE(SUM(battings.score), 0) >= 100 THEN ?
                        WHEN COALESCE(SUM(battings.score), 0) >= 50 THEN ?
                        WHEN COALESCE(SUM(battings.score), 0) >= 25 THEN ?
                        ELSE 0
                    END +
                    COALESCE(SUM(bowlings.wickets) * ?, 0) +
                    COALESCE(SUM(bowlings.medians) * ?, 0) +
                    CASE
                        WHEN COALESCE(SUM(bowlings.wickets), 0) >= 5 THEN ?
                        WHEN COALESCE(SUM(bowlings.wickets), 0) >= 3 THEN ?
                        WHEN COALESCE(SUM(bowlings.wickets), 0) >= 2 THEN ?
                        ELSE 0
                    END
                , 2) as total_points',
                [
                    $battingBonusForFour,
                    $battingBonusForSix,
                    $battingBonus100,
                    $battingBonus50,
                    $battingBonus25,
                    $bowlingWicket,
                    $bowlingMaiden,
                    $bowlingWicket5,
                    $bowlingWicket3,
                    $bowlingWicket2
                ]
            )
            ->groupBy('players.player_id', 'players.team_id')
            ->get();

        return $fantasyPoints;
    }

    public function createContest()
    {
        $matches = Fixture::upcoming()->pluck('fixture_id');
	$defaultContests = DefaultContest::where('is_cloneable', true)
        ->where('is_deleted', '!=', 1)
        ->get();
        DB::transaction(function () use ($defaultContests, $matches) {
            foreach ($matches as $match_id) {
                foreach ($defaultContests as $contest) {
                    $contests[] = [
                        'match_id' => $match_id,
                        'contest_type' => $contest->contest_type,
                        'default_contest_id' => $contest->id,
                        'total_winning_prize' => $contest->total_winning_prize,
                        'mrp' => $contest->mrp,
                        'entry_fees' => $contest->entry_fees,
                        'total_spots' => $contest->total_spots,
                        'first_prize' => $contest->first_prize,
                        'winner_percentage' => $contest->winner_percentage,
                        'prize_percentage' => $contest->prize_percentage,
                        'cancellation' => $contest->cancellation == 'YES',
                        'is_free' => $contest->is_free,
                        'usable_bonus' => $contest->usable_bonus,
                        'is_cancelable' => $contest->is_cancelable ?? 0,
                        'is_active' => $contest->deleted_at === null,
                    ];
                }
            }
            Contest::upsert($contests, ['match_id', 'default_contest_id'], [
                'total_winning_prize',
                'contest_type',
                'mrp',
                'entry_fees',
                'total_spots',
                'first_prize',
                'winner_percentage',
                'prize_percentage',
                'cancellation',
                'is_free',
                'usable_bonus',
                'is_cancelable',
                'is_active',
                'updated_at'
            ]);
        });
        return 'contest created';
    }

    public function setPointsRanks()
    {
        // $matches = Fixture::where('fixture_id',62242)->pluck('fixture_id');
        $livematchs = Fixture::where('is_live', true)
            ->select('fixture_id', 'status', 'localteam_id', 'visitorteam_id')
            ->addSelect('is_live', 'is_cancelled', 'is_completed')
            ->get();
        foreach ($livematchs as $match) {

            $createdTeams = JoinCrickContest::where('match_id', $match->fixture_id)->pluck('created_team_id');
            $usersTeams =  UserTeam::where('match_id', $match->fixture_id)->whereIn('id', $createdTeams)->get();
            // return Playerspoint::where('fixture_id', 62242)->get();
            foreach ($usersTeams as $team) {
                $main = Playerspoint::where('fixture_id', $match->fixture_id)
                    ->whereIn('player_id', $team->teams)
                    ->sum('points');
                $captainPoints = Playerspoint::where('fixture_id', $match->fixture_id)
                    ->where('player_id', $team->caption_id)
                    ->sum('points');
                $viceCaptainPoints = 0.5 * Playerspoint::where('fixture_id', $match->fixture_id)
                    ->where('player_id', $team->voic_caption_id)
                    ->sum('points');
                $bonus = $captainPoints + $viceCaptainPoints;
                $team->points = $main + $bonus;
                $team->save();
                JoinCrickContest::where(['match_id' => $match->fixture_id, 'created_team_id' => $team->id, 'user_id' => $team->user_id])->update([
                    'points' => $main + $bonus,
                ]);
            }
            // if ($match->status == 'Finished') {
            //     $match->is_live = false;
            //     $match->is_completed = true;
            //     $match->update();
            // }
            // if ($match->status == "Aban.") {
            //     $match->is_live = false;
            //     $match->is_cancelled = true;
            //     $match->is_completed = false;
            //     $match->update();
            // }
        }
    }
    public function rankGenerate()
    {
        $fixtures = Fixture::where('is_live', true)->get();
        // $fixtures = Fixture::where('fixture_id',65546)->get();
        foreach ($fixtures as $match) {
            $contests = Contest::where(['match_id' => $match->fixture_id, 'is_cancelled' => false, 'is_active' => true])->pluck('id');
            foreach ($contests as $contest) {
                $JoindContest = JoinCrickContest::where(['match_id' => $match->fixture_id, 'contest_id' => $contest])
                    ->orderBy('points', 'desc')
                    ->pluck('points');
                if (count($JoindContest) > 0) {
                    $pointsString = $JoindContest->implode(',');
                    $entries = DB::table('join_crick_contests')
                        ->select('id', 'user_id', 'created_team_id', 'points')
                        ->selectRaw("FIND_IN_SET(points, '$pointsString') as ranks")
                        ->where(['match_id' => $match->fixture_id, 'contest_id' => $contest])
                        ->orderBy('ranks', 'asc')
                        ->get();
                    foreach ($entries as $entry) {
                        JoinCrickContest::where('id', $entry->id)->update(['ranks' => $entry->ranks]);
                    }
                }
                if ($match->status == "Finished") {
                    $match->is_live = false;
                    $match->is_completed = true;
                    $match->update();
                }
                if ($match->status == "Aban.") {
                    $match->is_live = false;
                    $match->is_cancelled = true;
                    $match->is_completed = false;
                    $match->update();
                }
            }
        }
    }
    public function prizeDistribute()
    {
        $fixtures = Fixture::where(['is_completed' => true, 'is_prize_distributed' => false])->get();
        // $fixtures = Fixture::where('fixture_id',65546)->get();
        foreach ($fixtures as  $match) {
            $contests = Contest::where(['match_id' => $match->fixture_id, 'is_cancelled' => false])->with('prizeBreakups')->get();
            foreach ($contests as $contest) {
                if (isset($contest->prizeBreakups)) {
                    $lastRankUpto = $contest->prizeBreakups->last()->rank_upto;
                    $JoindContest = JoinCrickContest::where(['match_id' => $match->fixture_id, 'contest_id' => $contest->id])
                        ->with('user')
                        ->where('ranks', '<=', $lastRankUpto)
                        ->orderBy('ranks', 'asc')
                        ->get();

                    foreach ($JoindContest as $key => $data) {
                        $amount = $this->PrizeForRank($data->ranks, $contest->prizeBreakups, $match->fixture_id, $contest->id);
                        $data->winning_amount = round($amount, 2);
                        $data->update();
                        if ($data->user->role == 2) {
                            UserWallet::where('user_id', $data->user_id)
                                ->increment('winning', $amount);
                            Transection::create([
                                'user_id' => $data->user_id,
                                'type' => 1,
                                'amount' => $amount,
                                'desc' => 'Contest winning | ' . $match->localteam_code . ' - ' . $match->visitorteam_code,
                            ]);
                        }
                    }
                }
            }
            $match->is_prize_distributed = true;
            $match->update();
        }
        // return $fixtures;
    }
    protected function PrizeForRank($rank, $prizeBreakups, $match, $contest)
    {
        $prizeTier = collect($prizeBreakups)->first(function ($prize) use ($rank) {
            return $rank >= $prize['rank_from'] && $rank <= $prize['rank_upto'];
        });
        if (!$prizeTier) {
            return 0;
        }
        $sameRankCount = JoinCrickContest::where([
            'match_id' => $match,
            'contest_id' => $contest,
            'ranks' => $rank
        ])->count();
        if ($sameRankCount > 1) {
            $prizePool = collect($prizeBreakups)
                ->where('rank_from', '<=', $rank)
                ->where('rank_upto', '>=', $rank)
                ->sum('prize_amount');

            return $prizePool / $sameRankCount;
        }
        return $prizeTier['prize_amount'];
    }

    public function botJoinContest()
    {
        $matches = Fixture::whereDate('starting_at', Carbon::today())
            ->whereNotIn('status', ['Aban.'])
            ->where('is_completed', false)
            ->where('is_cancelled', false)
            ->orderby('starting_at', 'asc')
            ->whereBetween('starting_at', [Carbon::now(), Carbon::now()->addHour()])
            // ->select('fixture_id', 'season_id')
            ->get();
        foreach ($matches as $match) {
            $contests = Contest::where('match_id', $match->fixture_id)
                ->whereNotIn('contest_type', [2, 6])
                ->with('contestType', 'defaultContest')
                ->where(function ($query) {
                    $query->whereRaw('filled_spot < total_spots');
                })
                ->get();
            foreach ($contests as $contest) {
                $this->joinBotUserInContest($match, $contest);
            }
            // $players = Player::where('season_id',$match->season_id)->whereIn('team_id',[$match->localteam_id,$match->visitorteam_id])->;
        }
    }

    private function JoinBotUserInContest($match, $contest)
    {
        while (botsAllowedInContest($contest->match_id, $contest, $contest->contestType)) {
            $this->joinbotToContest($match, $contest);
        }
    }

    private function joinbotToContest($match, $contest)
    {
        $botUser = User::where('role', 3)->inRandomOrder()->first();
        if (countBotUserJoinedInContestForMatch($contest->match_id, $contest->id, $botUser->id) < $contest->contestType->max_entries) {
            $this->createBotUserTeamAndJoin($match, $contest, $botUser);
        }
    }

    protected function createBotUserTeamAndJoin($match, $contest, $botUser)
    {
        $players = Player::where('season_id', $match->season_id)
            ->whereIn('team_id', [$match->localteam_id, $match->visitorteam_id])
            ->inRandomOrder()
            ->limit(11)
            ->pluck('player_id')
            ->toArray();

        if (count($players) < 11) {
            return null;
        }
        $captain = $players[array_rand($players)];
        $viceCaptain = $players[array_rand(array_diff($players, [$captain]))];
        $team =  UserTeam::create([
            'match_id' => $match->fixture_id,
            'user_id' => $botUser->id,
            'name' => $botUser->name,
            'team_id' => [$match->localteam_id, $match->visitorteam_id],
            'caption_id' => $captain,
            'voic_caption_id' => $viceCaptain,
            'teams' => $players,
        ]);
        $joined =  JoinCrickContest::create([
            'match_id' => $match->fixture_id,
            'user_id' => $botUser->id,
            'contest_id' => $contest->id,
            'created_team_id' => $team->id,
            'entryfee_bonus' => $contest->usable_bonus,
            'entryfee_deposit' => $contest->entry_fees,
        ]);
        if ($joined) {
            $contest->filled_spot++;
            $contest->update();
        }
        return true;
    }
    
    public function resetBotsTeams()
    {
        $matches = Fixture::whereDate('starting_at', Carbon::today())
            ->whereNotIn('status', ['Aban.'])
            ->where('is_completed', false)
            ->where('is_cancelled', false)

            ->orderby('starting_at', 'asc')
            ->get();
        foreach ($matches as $match) {
            $botTeams = UserTeam::where('match_id', $match->fixture_id)
                ->whereHas('user', function ($query) {
                    $query->where('role', 3);
                })
                ->get();
            foreach ($botTeams as $team) {
                $players = Player::where('season_id', $match->season_id)
                    ->whereIn('team_id', [$match->localteam_id, $match->visitorteam_id])
                    ->inRandomOrder()
                    ->limit(11)
                    ->pluck('player_id')
                    ->toArray();
                if (count($players) < 11) {
                    return null;
                }
                $captain = $players[array_rand($players)];
                $viceCaptain = $players[array_rand(array_diff($players, [$captain]))];
                $team->update([
                    'caption_id' => $captain,
                    'voic_caption_id' => $viceCaptain,
                    'edit_count' => $team->edit_count + 1,
                    'teams' => $players,
                ]);
            }
        }
    }
}
