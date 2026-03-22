<?php

namespace App\Http\Controllers\Sports;

use Carbon\Carbon;
use App\Models\TypesValue;
use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\FootballScores;
use App\Models\FootballSeason;
use App\Models\FootballPlayers;
use App\Models\FootballPlaying11;
use App\Jobs\ProcessPlayerDetails;
use Illuminate\Support\Facades\DB;
use App\Models\FootballMatchEvents;
use App\Models\FootballParticipant;
use App\Models\FootballTeamPlayers;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\FootballLeague;
use App\Models\FootballPlayersPoints;
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
                    'league_id' => $match['league_id'],
                    'season_id' => $match['season_id'],
                    'stage_id' => $match['stage_id'],
                    'round_id' => $match['round_id'],
                    'venue_id' => $match['venue_id'],
                    'name' => $match['name'],
                    'starting_at' => Carbon::parse($match['starting_at']),
                    'length' => $match['length'],
                    'result_info' => $match['result_info'] ?? '',
                    'leg' => $match['leg'] ?? '',
                    'placeholder' => $match['placeholder'],
                    'has_odds' => $match['has_odds'],
                    'has_premium_odds' => $match['has_premium_odds'],
                    'starting_at_timestamp' => $match['starting_at_timestamp'],
                    'is_upcomming' => Carbon::parse($match['starting_at'])->greaterThan(now()),
                ]);
                if(isset($match['league'])){
                    $league = $match['league'];
                    FootballLeague::updateOrCreate([
                        'league_id' => $league['id'],
                    ],[
                        'country_id' => $league['country_id'],
                        'name' => $league['name'],
                        'active' => $league['active'],
                        'short_code' => $league['short_code'],
                        'image_path' => $league['image_path'],
                        'type' => $league['type'],
                        'sub_type' => $league['sub_type'],
                        'last_played_at' => $league['last_played_at'],
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
                            'country_id' => $team['country_id'],
                            'venue_id' => $team['venue_id'],
                            'gender' => $team['gender'],
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
        $matches = FootballMatch::get()->pluck('match_id')->implode(',');
        $response =  $this->footballApi->updateMatches($matches);
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
                FootballMatch::where('match_id', $match['id'])->update([
                    'is_upcomming' => $startingAt->greaterThan($current),
                    'is_live' => !isset($match['result_info']) && $startingAt->lessThan($current),
                    // 'is_completed' => isset($match['result_info']),
                ]);
            }
        }
        // }
    }
    public function getTeamWithPlayers()
    {
        $seasonIDs = FootballSeason::where(['finished' => false])->pluck('season_id');
        $playersData = [];
        foreach ($seasonIDs as $season_id) {
            $response =  $this->footballApi->geteamswithPlayes($season_id);
            if ($response['success']) {
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
                    }
                }
            }
        }
        if (!empty($playersData)) {
            // return $playersData;
            DB::transaction(function () use ($playersData) {
                FootballTeamPlayers::upsert(
                    $playersData,
                    [
                        'player_id',
                        'team_id',
                        'season_id',
                    ],
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
            return $playersData;
        }
    }

    public function getPlayerDetails()
    {
        ProcessPlayerDetails::dispatch($chunkSize = 100)->onQueue('player-processing');
        $seasonIDs = FootballSeason::where(['finished' => false])->pluck('season_id');

        // Solution 1: Get all player IDs first, then process them in chunks
        $playerIds = FootballTeamPlayers::whereIn('season_id', $seasonIDs)
            ->select('player_id')
            ->distinct()
            ->pluck('player_id');
        return count($playerIds);
        return "hello";
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
}
