<?php

namespace App\Http\Controllers\Sports;

use Carbon\Carbon;
use App\Models\UserTeam;
use App\Models\TypesValue;
use App\Models\UserWallet;
use App\Models\Transection;
use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\FootballLeague;
use App\Models\FootballScores;
use App\Models\FootballSeason;
use App\Models\FootballContest;
use App\Models\FootballPlayers;
use App\Models\FootballPlaying11;
use App\Jobs\ProcessPlayerDetails;
use Illuminate\Support\Facades\DB;
use App\Models\FootballJoinContest;
use App\Models\FootballMatchEvents;
use App\Models\FootballParticipant;
use App\Models\FootballTeamPlayers;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\FootballPlayersPoints;
use App\Models\FootballDefaultContest;
use App\Services\PlayerDetailsService;
use App\Services\FootballSportsService;
use App\Services\SportsMonkBaseService;

class FootballSportsController extends Controller
{

    protected $footballApi;

    public function __construct()
    {
        $this->footballApi = new FootballSportsService();
    }
    public function getMatches(Request $request)
    {
        $response =  $this->footballApi->getfixtures();
        if ($response['success']) {
            foreach ($response['data'] as $match) {
                FootballMatch::updateOrCreate(["match_id" => $match['id']], [
                    "match_id" => $match['id'],
                    'league_id' => $match['league_id'] ?? 0,
                    'season_id' => $match['season_id'] ?? 0,
                    'stage_id' => $match['stage_id'] ?? 0,
                    'round_id' => $match['round_id'] ?? 0, // <-- fix here
                    'venue_id' => $match['venue_id'] ?? 0,
                    'name' => $match['name'] ?? '',
                    'starting_at' => Carbon::parse($match['starting_at']),
                    'length' => $match['length'] ?? 0,
                    'result_info' => $match['result_info'] ?? '',
                    'leg' => $match['leg'] ?? '',
                    'placeholder' => $match['placeholder'] ?? 0,
                    'status' => $match['state']['developer_name'],
                    'has_odds' => $match['has_odds'] ?? 0,
                    'has_premium_odds' => $match['has_premium_odds'] ?? 0,
                    'starting_at_timestamp' => $match['starting_at_timestamp'] ?? 0,
                    'is_upcomming' => Carbon::parse($match['starting_at'])->greaterThan(now()),
                ]);

                if (isset($match['league'])) {
                    $league = $match['league'];
                    FootballLeague::updateOrCreate([
                        'league_id' => $league['id'],
                    ], [
                        'country_id' => $league['country_id'],
                        'name' => $league['name'] ?? '',
                        'active' => $league['active'] ?? 1,
                        'short_code' => $league['short_code'] ?? '',
                        'image_path' => $league['image_path'] ?? '',
                        'type' => $league['type'] ?? '',
                        'sub_type' => $league['sub_type'] ?? '',
                        'last_played_at' => $league['last_played_at'] ?? null,
                    ]);
                }
                if (isset($match['season'])) {
                    $season = $match['season'];
                    FootballSeason::updateOrCreate(['season_id' => $season['id']], [
                        'league_id' => $season['league_id'],
                        'tie_breaker_rule_id' => $season['tie_breaker_rule_id'],
                        'name' => $season['name'],
                        'finished' => $season['finished'],
                        'pending' => $season['pending'],
                        'is_current' => $season['is_current'],
                        'starting_at' => $season['starting_at'],
                        'ending_at' => $season['ending_at']
                    ]);
                }
                if (isset($match['participants'])) {
                    foreach ($match['participants'] as $team) {
                        FootballParticipant::updateOrcreate(["match_id" => $match['id'], 'team_id' => $team['id']], [
                            'country_id' => $team['country_id'] ?? 0,
                            'venue_id' => $team['venue_id'] ?? 0,
                            'gender' => $team['gender'] ?? '',
                            'name' => $team['name'],
                            'short_code' => $team['short_code'],
                            'image_path' => $team['image_path'],
                            'founded' => $team['founded'],
                            'type' => $team['type'],
                            'last_played_at' => $team['last_played_at'],
                            'location' => $team['meta']['location'],
                            'position' => $team['meta']['position']
                        ]);
                    }
                }
            }
            return "All match updated successfully.";
        } else {
            return "Some Error =>" . json_encode($response);
        }
    }
    public function updateMatch()
    {
        //    return $matches = FootballMatch::needUpdate()->get()->pluck('match_id');
        $matches = FootballMatch::needUpdate()->get()->pluck('match_id');
        if (count($matches) > 0) {
            $response =  $this->footballApi->updateMatches($matches->implode(','));
            if ($response['success']) {
                foreach ($response['data'] as $key => $match) {
                    if (count($match['lineups']) > 0) {
                        foreach ($match['lineups'] as $key => $lineup) {
                            if (isset($lineup['player_id']) && isset($lineup['fixture_id']) && isset($lineup['team_id'])) {
                                FootballPlaying11::updateOrcreate([
                                    'match_id' => $lineup['fixture_id'],
                                    'player_id' => $lineup['player_id'],
                                    'team_id' => $lineup['team_id'],
                                ], [
                                    'sport_id' => $lineup['sport_id'] ?? 1,
                                    'position_id' => $lineup['position_id'] ?? 0,
                                    'detailed_position_id' => $lineup['detailed_position_id'] ?? 0,
                                    'formation_field' => $lineup['formation_field'] ?? '',
                                    'type_id' => $lineup['type_id'] ?? 0,
                                    'jersey_number' => $lineup['jersey_number'] ?? 0,
                                    'formation_position' => $lineup['formation_position'] ?? 0,
                                    'player_name' => $lineup['player_name'] ?? '',
                                ]);
                            }
                            typeStore($lineup['detailedposition']);
                        }
                    }
                    if (isset($match['season'])) {
                        $season = $match['season'];
                        FootballSeason::updateOrCreate(['season_id' => $season['id']], [
                            'league_id' => $season['league_id'],
                            'tie_breaker_rule_id' => $season['tie_breaker_rule_id'],
                            'name' => $season['name'],
                            'finished' => $season['finished'],
                            'pending' => $season['pending'],
                            'is_current' => $season['is_current'],
                            'starting_at' => $season['starting_at'],
                            'ending_at' => $season['ending_at']
                        ]);
                    }
                    if (count($match['scores']) > 0) {
                        foreach ($match['scores'] as $key => $score) {
                            FootballScores::updateOrcreate([
                                'match_id' => $score['fixture_id'],
                                'participant_id' => $score['participant_id'],
                                'type_id' => $score['type_id'],
                            ], [
                                'score' => $score['score']['goals'] ?? 0,
                                'participant' => $score['score']['participant'] ?? '',
                                'description' => $score['description'] ?? '',
                            ]);
                            typeStore($score['type']);
                        }
                    }
                    if (count($match['events']) > 0) {
                        foreach ($match['events'] as $key => $event) {
                            try {
                                FootballMatchEvents::updateOrCreate(
                                    [
                                        'event_id' => $event['id'],
                                    ],
                                    [
                                        'match_id' => $event['fixture_id'],
                                        'period_id' => $event['period_id'],
                                        'participant_id' => $event['participant_id'],
                                        'type_id' => $event['type_id'],
                                        'player_id' => $event['player_id'] ?? 0,
                                        'related_player_id' => $event['related_player_id'] ?? 0,
                                        'player_name' => $event['player_name'] ?? '',
                                        'related_player_name' => $event['related_player_name'] ?? 0,
                                        'result' => $event['result'] ?? '',
                                        'info' => $event['info'] ?? '',
                                        'addition' => $event['addition'] ?? '',
                                        'minute' => $event['minute'] ?? 0,
                                        'extra_minute' => $event['extra_minute'] ?? 0,
                                        'injured' => $event['injured'] ?? false,
                                        'team_id' => $event['participant_id'],
                                    ]
                                );
                                typeStore($event['type']);
                            } catch (\Exception $e) {
                                Log::error("Failed to process event {$event['id']}: " . $e->getMessage());
                                continue;
                            }
                        }
                    }
                    $startingAt = Carbon::parse($match['starting_at']);
                    $current = now();
                    $winningTeamId = 0;
                    if (!empty($match['participants'])) {
                        foreach ($match['participants'] as $team) {
                            if (!empty($team['meta']['winner']) && $team['meta']['winner'] === true) {
                                $winningTeamId = $team['id'];
                                break;
                            }
                        }
                    }
                    FootballMatch::where('match_id', $match['id'])->update([
                        'status' => $match['state']['developer_name'] ?? 'NA',
                        'is_upcomming' => $startingAt->greaterThan($current),
                        // 'is_live' => !isset($match['result_info']) && $startingAt->lessThan($current),
                        'is_live' => $startingAt->lessThan($current),
                        'winning_team_id' => $winningTeamId,
                    ]);
                }
            }
        }
        return "match updated.";
        // }
    }
    public function getTeamWithPlayers()
    {
        $seasonIDs = FootballSeason::where(['finished' => false])->pluck('season_id');
        $playersData = [];
        $failedSeasons = [];

        foreach ($seasonIDs as $season_id) {
            $response = $this->footballApi->geteamswithPlayes($season_id);

            if (!$response['success'] || empty($response['data'])) {
                $failedSeasons[] = $season_id;
                continue;
            }
            foreach ($response['data'] as $key => $data) {
                if (!isset($data['players'])) {
                    continue;
                }
                foreach ($data['players'] as $key => $players) {
                    $playersData[] = [
                        'player_id' => $players['player_id'],
                        'season_id' =>  $season_id,
                        'team_id' =>  $players['team_id'],
                        'transfer_id' =>  $players['transfer_id'] ?? 0,
                        'position_id' =>  $players['position_id'] ?? 0,
                        'detailed_position_id' =>  $players['detailed_position_id'] ?? 0,
                        'start' => Carbon::parse($players['start']),
                        'end' => Carbon::parse($players['end']),
                        'captain' =>  $players['captain'] = true,
                        'jersey_number' =>  $players['jersey_number'] ?? 0,
                        'position_name' =>  $players['position']['name'] ?? '',
                        'position_code' =>  $players['position']['code'] ?? '',
                        'position_developer_name' =>  $players['position']['developer_name'] ?? '',
                        'position_model_type' =>  $players['position']['model_type'] ?? '',
                        'position_stat_group' =>  $players['position']['stat_group'] ?? '',
                    ];

                    if (isset($players['player'])) {
                        $playerInfo = $this->formatPlayerData($players['player']);
                        FootballPlayers::updateOrCreate(
                            ['player_id' => $playerInfo['player_id'], 'sport_id' => $playerInfo['sport_id']],
                            $playerInfo
                        );
                    }
                }
            }
        }

        if (!empty($playersData)) {
            DB::transaction(function () use ($playersData) {
                collect($playersData)->chunk(500)->each(function ($chunk) {
                    FootballTeamPlayers::upsert(
                        $chunk->all(),
                        ['player_id', 'team_id', 'season_id'],
                        [
                            'transfer_id',
                            'position_id',
                            'detailed_position_id',
                            'start',
                            'end',
                            'captain',
                            'jersey_number',
                            'position_name',
                            'position_code',
                            'position_developer_name',
                            'position_model_type',
                            'position_stat_group',
                        ]
                    );
                });
            });

            return "Player with team updated.";
        }

        if (!empty($failedSeasons)) {
            // logger()->warning('These season_ids returned empty data:', $failedSeasons);
            // // Or simply:
            // dd('Empty API response for season_ids: ', $failedSeasons);
            return $failedSeasons;
        }
    }

    // public function getPlayerDetails()
    // {
    //     return response()->stream(function () {
    //         $service = new \App\Services\PlayerDetailsService();

    //         $callback = function ($message) {
    //             static $init = false;
    //             if (!$init) {
    //                 echo str_repeat(" ", 2048); // Initial buffer to trigger streaming
    //                 $init = true;
    //             }

    //             echo $message . PHP_EOL;
    //             ob_flush();
    //             flush();
    //         };

    //         $service->syncPlayerDetailsWithProgress($callback, 50); // Process in chunks of 50
    //     }, 200, [
    //         'Content-Type' => 'text/plain',
    //         'Cache-Control' => 'no-cache',
    //         'X-Accel-Buffering' => 'no', // Required for NGINX
    //     ]);
    // }
    protected function formatPlayerData($data)
    {
        return [
            'player_id' => $data['id'],
            'sport_id' => $data['sport_id'],
            'country_id' => $data['country_id'] ?? 0,
            'nationality_id' => $data['nationality_id'] ?? 0,
            'city_id' => $data['city_id'] ?? '',
            'position_id' => $data['position_id'] ?? 0,
            'detailed_position_id' => $data['detailed_position_id'] ?? 0,
            'type_id' => $data['type_id'] ?? 0,
            'common_name' => $data['common_name'] ?? '',
            'firstname' => $data['firstname'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'name' => $data['name'] ?? '',
            'display_name' => $data['display_name'] ?? '',
            'image_path' => $data['image_path'] ?? '',
            'height' => $data['height'] ?? 0,
            'weight' => $data['weight'] ?? 0,
            'date_of_birth' => $data['date_of_birth'] ?? '1000-01-01',
            'gender' => $data['gender'] ?? '',
        ];
    }
    public function makePlayerPoints()
    {
        $matches = FootballMatch::get();
        foreach ($matches as $key => $match) {
            $eventTypes = [14, 15, 17, 18, 19, 20];
            $events = FootballMatchEvents::where('match_id', $match->match_id)
                ->whereIn('type_id', $eventTypes)
                ->get(['type_id', 'player_id', 'related_player_id']);

            $playerCounts = [];
            $relatedPlayerCounts = [];
            $yellowCardCounts = [];
            $redCardCounts = [];
            $ownGoalCounts = [];
            $penaltyMissCounts = [];
            $substitutionCounts = [];

            // Tally all counts in a single loop
            foreach ($events as $event) {
                switch ($event->type_id) {
                    case 14: // Goal
                        if ($event->player_id) {
                            $playerCounts[$event->player_id] = ($playerCounts[$event->player_id] ?? 0) + 1;
                        }
                        if ($event->related_player_id) {
                            $relatedPlayerCounts[$event->related_player_id] = ($relatedPlayerCounts[$event->related_player_id] ?? 0) + 1;
                        }
                        break;
                    case 15: // Own goal
                        $ownGoalCounts[$event->player_id] = ($ownGoalCounts[$event->player_id] ?? 0) + 1;
                        break;
                    case 17: // Penalty miss
                        $penaltyMissCounts[$event->player_id] = ($penaltyMissCounts[$event->player_id] ?? 0) + 1;
                        break;
                    case 18: // Substitution
                        $substitutionCounts[$event->player_id] = ($substitutionCounts[$event->player_id] ?? 0) + 1;
                        break;
                    case 19: // Yellow card
                        $yellowCardCounts[$event->player_id] = ($yellowCardCounts[$event->player_id] ?? 0) + 1;
                        break;
                    case 20: // Red card
                        $redCardCounts[$event->player_id] = ($redCardCounts[$event->player_id] ?? 0) + 1;
                        break;
                }
            }

            // Get players with positions
            $players = FootballPlaying11::where('match_id', $match->match_id)
                ->get(['player_id', 'position_id']);

            $playerPositions = $players->pluck('position_id', 'player_id');

            $pointsMap = [];
            $striker = [163, 27, 151];
            $midfielder = [157, 158, 153, 150, 149, 26];
            $defender_goalkeeper = [155, 156, 148, 25, 24];

            // Goal points based on position
            foreach ($playerCounts as $playerId => $count) {
                $pos = $playerPositions[$playerId] ?? null;
                if (in_array($pos, $striker)) {
                    $pointsMap[$playerId] = 40 * $count;
                } elseif (in_array($pos, $midfielder)) {
                    $pointsMap[$playerId] = 50 * $count;
                } elseif (in_array($pos, $defender_goalkeeper)) {
                    $pointsMap[$playerId] = 60 * $count;
                }
            }

            // Assists (related player)
            foreach ($relatedPlayerCounts as $playerId => $count) {
                $pointsMap[$playerId] = ($pointsMap[$playerId] ?? 0) + (20 * $count);
            }

            // Substitution +2
            foreach ($substitutionCounts as $playerId => $count) {
                $pointsMap[$playerId] = ($pointsMap[$playerId] ?? 0) + (2 * $count);
            }

            // Starting 11 bonus
            $starting11 = FootballPlaying11::where([
                'match_id' => $match->match_id,
                'type_id' => 11
            ])->pluck('player_id');

            foreach ($starting11 as $playerId) {
                $pointsMap[$playerId] = ($pointsMap[$playerId] ?? 0) + 4;
            }

            // Yellow cards -4
            foreach ($yellowCardCounts as $playerId => $count) {
                $pointsMap[$playerId] = ($pointsMap[$playerId] ?? 0) - (4 * $count);
            }

            // Red cards -10
            foreach ($redCardCounts as $playerId => $count) {
                $pointsMap[$playerId] = ($pointsMap[$playerId] ?? 0) - (10 * $count);
            }

            // Own goals -8
            foreach ($ownGoalCounts as $playerId => $count) {
                $pointsMap[$playerId] = ($pointsMap[$playerId] ?? 0) - (8 * $count);
            }

            // Penalty misses -20
            foreach ($penaltyMissCounts as $playerId => $count) {
                $pointsMap[$playerId] = ($pointsMap[$playerId] ?? 0) - (20 * $count);
            }


            $playing11WithTeam = FootballPlaying11::where('match_id', $match->match_id)
                ->get(['player_id', 'team_id']);

            $playerTeams = $playing11WithTeam->pluck('team_id', 'player_id');

            // Insert or update player points
            $insertData = [];

            foreach ($pointsMap as $playerId => $points) {
                if (isset($playerTeams[$playerId])) {
                    $insertData[] = [
                        'match_id'  => $match->match_id,
                        'team_id'   => $playerTeams[$playerId],
                        'player_id' => $playerId,
                        'points'    => $points,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            print_r($insertData);
            echo "<br><hr>";
            FootballPlayersPoints::upsert(
                $insertData,
                ['player_id', 'team_id', 'match_id'],
                ['points']
            );
        }
    }
    public function createContest()
    {
        $matches = FootballMatch::where('status', 'NS')->pluck('match_id');
        $defaultContests = FootballDefaultContest::withTrashed()->where('is_cloneable', true)->get();
        if (count($defaultContests) > 0) {
            $contests = [];
            DB::transaction(function () use ($defaultContests, $matches) {
                foreach ($matches as $match_id) {
                    foreach ($defaultContests as $contest) {
                        $contests[] = [
                            'match_id' => $match_id,
                            'contest_type' => $contest->contest_type,
                            'contest_type_code' => $contest->contest_type_code,
                            'max_entries' => $contest->max_entries,
                            'default_contest_id' => $contest->id,
                            'total_winning_prize' => $contest->total_winning_prize,
                            'mrp' => $contest->mrp,
                            'entry_fees' => $contest->entry_fees,
                            'total_spots' => $contest->total_spots,
                            'first_prize' => $contest->first_prize,
                            'winner_percentage' => $contest->winner_percentage,
                            'prize_percentage' => $contest->prize_percentage,
                            'cancellation' => $contest->cancellation,
                            'is_free' => $contest->is_free,
                            'usable_bonus' => $contest->usable_bonus,
                            'is_cancelable' => $contest->is_cancelable ?? 0,
                            'is_active' => $contest->deleted_at === null,
                        ];
                    }
                }
                FootballContest::upsert($contests, ['match_id', 'default_contest_id'], [
                    'total_winning_prize',
                    'contest_type',
                    'contest_type_code',
                    'max_entries',
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
        }

        return 'contest created';
    }
    public function setPointsRanks()
    {
        $livematches = FootballMatch::where('is_live', true)->get();
        foreach ($livematches as $match) {
            $createdTeams = FootballJoinContest::where('match_id', $match->match_id)->pluck('created_team_id');
            if (count($createdTeams) > 0) {
                $usersTeams =  UserTeam::where('match_id', $match->match_id)->whereIn('id', $createdTeams)->get();
                foreach ($usersTeams as $team) {
                    $main = FootballPlayersPoints::where('match_id', $match->match_id)
                        ->whereIn('player_id', $team->teams)
                        ->sum('points');
                    $captainPoints = FootballPlayersPoints::where('match_id', $match->match_id)
                        ->where('player_id', $team->caption_id)
                        ->sum('points');
                    $viceCaptainPoints = 0.5 * FootballPlayersPoints::where('match_id', $match->match_id)
                        ->where('player_id', $team->voic_caption_id)
                        ->sum('points');
                    $bonus = $captainPoints + $viceCaptainPoints;
                    $team->points = $main + $bonus;
                    $team->save();
                    FootballJoinContest::where(['match_id' => $match->match_id, 'created_team_id' => $team->id, 'user_id' => $team->user_id])->update([
                        'points' => $main + $bonus,
                    ]);
                }
                $this->rankGenerate($match);
            } else {
                if ($match->status == "FT" || (isset($match->winning_team_id) && $match->winning_team_id != 0)) {
                    $match->is_live = false;
                    $match->is_completed = true;
                    $match->update();
                }
                if ($match->status == "ABANDONED") {
                    $match->is_live = false;
                    $match->is_cancelled = true;
                    $match->is_completed = false;
                    $match->update();
                }
            }
        }
    }
    protected function rankGenerate($match)
    {
        $contests = FootballContest::where(['match_id' => $match->match_id, 'is_cancelled' => false, 'is_active' => true])->pluck('id'); //'is_cancelled' => false, 'is_active' => true]
        foreach ($contests as $contest) {
            $JoindContest = FootballJoinContest::where([
                'match_id' => $match->match_id,
                'contest_id' => $contest
            ])->orderBy('points', 'desc')->get(['points']);
            if (count($JoindContest) > 0) {
                $pointsString = $JoindContest->pluck('points')->map(function ($point) {
                    return number_format($point, 2, '.', '');
                })->implode(',');
                $entries = DB::table('football_join_contests')
                    ->select('id', 'user_id', 'created_team_id', 'points')
                    ->selectRaw("FIND_IN_SET(points, '$pointsString') as ranks")
                    ->where([
                        'match_id' => $match->match_id,
                        'contest_id' => $contest
                    ])
                    ->orderBy('ranks', 'asc')
                    ->get();
                foreach ($entries as $entry) {
                    FootballJoinContest::where('id', $entry->id)->update(['ranks' => $entry->ranks]);
                }
            }
            if ($match->status == "FT" || (isset($match->winning_team_id) && $match->winning_team_id != 0)) {
                $match->is_live = false;
                $match->is_completed = true;
                $match->update();
            }
            if ($match->status == "ABANDONED") {
                $match->is_live = false;
                $match->is_cancelled = true;
                $match->is_completed = false;
                $match->update();
            }
        }
    }
    public function prizeDistribute()
    {
        $matches = FootballMatch::where(['is_completed' => true, 'is_prize_distributed' => false])->get();
        foreach ($matches as  $match) {
            $contests = FootballContest::where(['match_id' => $match->match_id, 'is_cancelled' => false])->with('prizeBreakups')->get();
            foreach ($contests as $contest) {
                if (isset($contest->prizeBreakups)) {
                    $lastRankUpto = $contest->prizeBreakups->last()->rank_upto;
                    $JoindContest = FootballJoinContest::where(['match_id' => $match->match_id, 'contest_id' => $contest->id])
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
                                'desc' => 'Contest winning | ' . $match->name,
                            ]);
                        }
                    }
                }
            }
            $match->is_prize_distributed = true;
            $match->update();
        }
    }
    protected function PrizeForRank($rank, $prizeBreakups, $match, $contest)
    {
        $prizeTier = collect($prizeBreakups)->first(function ($prize) use ($rank) {
            return $rank >= $prize['rank_from'] && $rank <= $prize['rank_upto'];
        });
        if (!$prizeTier) {
            return 0;
        }
        $sameRankCount = FootballJoinContest::where([
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

    public function cancelContest()
    {
        $matches = FootballMatch::whereDate('starting_at', Carbon::today())
            ->whereNotIn('status', ['Aban.'])
            ->where('is_completed', false)
            ->where('is_cancelled', false)
            ->orderby('starting_at', 'asc')
            ->whereBetween('starting_at', [Carbon::now()->subMinutes(5), Carbon::now()])
            ->get();

        foreach ($matches as $match) {
            $contests = FootballContest::where('match_id', $match->match_id)
                ->where(['is_cancelled' => false, 'is_cancelable' => true, 'is_active' => true,])
                ->where(function ($query) {
                    $query->whereRaw('filled_spot < total_spots');
                })
                ->get();
            foreach ($contests as $key => $contest) {
                if ($contest->is_cancelable && $this->contestInloss($contest)) {
                    $contest->is_cancelled = true;
                    $contest->update();
                    $joined = FootballJoinContest::where(['match_id' => $match->match_id, 'contest_id' => $contest->id])
                        ->whereHas('user', function ($query) {
                            $query->where('role', 2);
                        })
                        ->get();
                    DB::beginTransaction();
                    try {
                        foreach ($joined as $data) {
                            UserWallet::where('user_id', $data->user_id)
                                ->increment('balance', $data->entryfee_deposit)
                                ->increment('bonus', $data->entryfee_bonus);

                            Transection::create([
                                'user_id' => $data->user_id,
                                'type' => 1,
                                'amount' => $data->entryfee_deposit,
                                'desc' => 'Contest Cancelled | ' . $match->name,
                            ]);
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                }
            }
        }
    }
    protected function contestInloss($contest)
    {
        $joinedFee = FootballJoinContest::where('contest_id', $contest->id)
            ->where('match_id', $contest->match_id)
            ->whereHas('user', function ($query) {
                $query->where('role', 2);
            })
            ->sum('entryfee_deposit');
        return $contest->total_winning_prize < $joinedFee;
    }
}
