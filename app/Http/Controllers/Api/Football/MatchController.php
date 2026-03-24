<?php

namespace App\Http\Controllers\Api\Football;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Fixture;
use App\Models\UserTeam;
use App\Models\Transection;
use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\FootballScores;
use App\Models\FootballContest;
use Illuminate\Validation\Rule;
use App\Models\FootballPlaying11;
use Illuminate\Support\Facades\DB;
use App\Models\FootballJoinContest;
use App\Models\FootballParticipant;
use App\Models\FootballTeamPlayers;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\FootballLeague;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    public function index(Request $request, $status)
    {
        if ($status != 'upcoming') {
            return Helper::EmptyReturn('invalid data request.');
        }
        $notAllowedLeagues = FootballLeague::where('status', false)->pluck('league_id');
        $matches = FootballMatch::query()
        ->whereNotIn('league_id', $notAllowedLeagues)
        ->withLeagueAndParticipants();
        $matches = $matches->where('status', 'NS')->where('is_upcomming', true)->where('is_cancelled', false)->orderby('starting_at', 'asc');
        $matches = $matches->take(20)->get();
        return Helper::SuccessReturn($matches, 'match list fatched.');
    }

    public function matchdetails(Request $request, $match_id)
    {
        $match = FootballMatch::withLeagueAndParticipants()->where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::EmptyReturn('Invalid match details');
        }
        return Helper::SuccessReturn($match, 'matchdetails');
    }
    public function contests(Request $request, $match_id)
    {
        $match = FootballMatch::where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::EmptyReturn('Invalid match details');
        }
        $contests = FootballContest::where('match_id', $match_id)->active()
            ->with('prizeBreakups')
            ->orderby('total_winning_prize', 'desc')
            ->get();
        $user = Auth::guard('api')->user();
        if ($user) {
            $userContests = FootballJoinContest::where([
                'match_id' => $match_id,
                'user_id' => $user->id
            ])
                ->get()
                ->groupBy('contest_id')
                ->map(function ($group) {
                    $teamIds = array_map('intval', explode(',', $group->pluck('created_team_id')->implode(',')));
                    return [
                        'contest_id' => $group->first()->contest_id,
                        'teams' => $teamIds
                    ];
                })
                ->values();
        } else {
            $userContests  = [];
        }

        return Helper::SuccessReturn(['contests' => $contests, 'userContests' => $userContests], 'contest list fatched.');
    }

    public function addJoinContest(Request $request, $match_id)
    {
        $match = FootballMatch::where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::EmptyReturn('Invalid request');
        }
        $rules = [
            'team_id' => ['required', Rule::exists('user_teams', 'id')],
            'contest_id' => ['required', Rule::exists('football_contests', 'id')->where('is_active', true)],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Helper::EmptyReturn($validator->errors()->first());
        }
        if (Carbon::parse($match->starting_at)->lessThanOrEqualTo(now())) {
            return Helper::EmptyReturn('Match is live or already started. You can no longer join this contest.');
        }
        $contest = FootballContest::where('id', $request->contest_id)->active()->with('defaultContest', 'prizeBreakups')->first();
        if ($contest->is_cancelled) {
            return Helper::EmptyReturn('Contest cancelled.');
        }

        if ($contest->filled_spot == $contest->total_spots) {
            return Helper::EmptyReturn('Contest already filled.');
        }
        $user = auth()->user();
        if ($contest->max_entries == joinedFootballTeamCount($user->id, $match_id, $request->contest_id)) {
            return Helper::EmptyReturn('Already used max allowed team.');
        }
        if (alreayFootballJoinedContestWithTeam($user->id, $request->team_id, $match_id, $request->contest_id)) {
            return Helper::EmptyReturn('Already joined this contest using this team.');
        }
        try {
            DB::beginTransaction();

            if (!$contest->defaultContest->is_free) {
                $user->load('account');
                $usableBonus = $contest->defaultContest->usable_bonus; // max usable bonus
                $availableBonus = $user->account->bonus;
                $deductBonus = min($usableBonus, $availableBonus, $contest->entry_fees);
                $remainingFees = $contest->entry_fees - $deductBonus;

                if ($remainingFees > ($user->account->balance + $user->account->winning)) {
                    return Helper::EmptyReturn('Insufficient balance. Please recharge first.');
                }

                $user->account->bonus -= $deductBonus;

                if ($user->account->balance < $remainingFees) {
                    $amountFromWinnings = $remainingFees - $user->account->balance;
                    $user->account->winning -= $amountFromWinnings;
                    $user->account->balance = 0;
                } else {
                    $user->account->balance -= $remainingFees;
                }

                $user->account->save();

                Transection::create([
                    'user_id' => $user->id,
                    'type' => 2,
                    'amount' => $contest->entry_fees,
                    'desc' => 'Contest Entry | ' . $match->name,
                ]);
            } else {
                $deductBonus = 0;
            }

            $data = FootballJoinContest::create([
                'match_id' => $match_id,
                'user_id' => $user->id,
                'contest_id' => $request->contest_id,
                'created_team_id' => $request->team_id,
                'entryfee_bonus' => $deductBonus,
                'entryfee_deposit' => $contest->entry_fees,
            ]);

            $contest->filled_spot += 1;
            $contest->save();

            DB::commit();
            return Helper::SuccessReturn([], 'Contest joined successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contest join failed', ['error' => $e->getMessage()]);
            return Helper::EmptyReturn('Failed to join contest. Please try again.');
        }
    }

    public function teamsData(Request $request, $match_id)
    {
        $match = FootballMatch::where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::EmptyReturn('Invalid request');
        }
        $teams = FootballParticipant::where('match_id', $match_id)->pluck('team_id');
        $players = FootballTeamPlayers::join('football_players', 'football_team_players.player_id', '=', 'football_players.player_id')
            ->where('football_team_players.season_id', $match->season_id)
            ->whereIn('football_team_players.team_id', $teams)
            ->select(
                'football_team_players.team_id',
                'football_team_players.position_id',
                'football_team_players.jersey_number',
                'football_team_players.captain',
                'football_team_players.points',
                'football_players.player_id',
                'football_players.common_name',
                'football_players.firstname',
                'football_players.lastname',
                'football_players.name',
                'football_players.display_name',
                'football_players.image_path',
                // 'football_players.detailed_position_id',
                // 'football_players.country_id',
                // 'football_players.nationality_id',
                // 'football_players.city_id',
                // 'football_players.type_id',        
                // 'football_players.height',
                // 'football_players.weight',
                // 'football_players.date_of_birth',
                // 'football_players.gender'
            )
            ->get();
        return Helper::SuccessReturn($players, 'Players list.');
        return $players;
    }

    public function getTeam(Request $request, $match_id)
    {
        $match = FootballMatch::where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::EmptyReturn('Invalid request');
        }
        $user = auth()->user();
        $data = $this->_getUserMatchTeams($match_id, $match->season_id, $user->id);
        return Helper::SuccessReturn($data, 'your Team list for match.');
    }

    public function createTeam(Request $request, $match_id)
    {
        $match = FootballMatch::where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::EmptyReturn('Invalid request');
        }
        $teams = FootballParticipant::where('match_id', $match_id)->pluck('team_id');
        $rules = [
            'players' => ['required', 'array', 'size:11'],
            'players.*' => [Rule::exists('football_team_players', 'player_id')->where(function ($query) use ($match, $teams) {
                $query->where('season_id', $match->season_id)
                    ->whereIn('team_id', $teams);
            })],
            'c_player' => ['required'],
            'vc_player' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules, [
            'players.size' => 'Please select 11 players for complition of team.',
            'players.*.exists' => 'Invalid team player selected.',
            'c_player.required' => 'Caption selection required in team',
            'vc_player.required' => 'Voice caption selection required in team',
        ]);
        if ($validator->fails()) {
            return Helper::EmptyReturn(null, $validator->errors()->first());
        }
        $user = auth()->user();
        if ($request->c_player == $request->vc_player) {
            return Helper::EmptyReturn(null, 'Caption and voice caption player cannot be one player.');
        }
        $playersLimit = DB::table('football_team_players')
            ->whereIn('player_id', $request->players)
            ->where('season_id', $match->season_id)
            ->whereIn('team_id', $teams)
            ->groupBy('team_id')
            ->select('team_id')
            ->havingRaw('COUNT(*) > 7')
            ->exists();
        if ($playersLimit) {
            return Helper::EmptyReturn('7 players max allowed from one team.');
        }
        UserTeam::create([
            'match_id' => $match_id,
            'user_id' => $user->id,
            'name' => $user->name,
            'team_id' => $teams,
            'caption_id' => $request->c_player,
            'voic_caption_id' => $request->vc_player,
            'teams' => $request->players,
        ]);
        $data = $this->_getUserMatchTeams($match_id, $match->season_id, $user->id);
        return Helper::SuccessReturn($data, 'Team created successfully.');
    }
    protected function _getUserMatchTeams($match_id, $season, $user_id)
    {
        // return UserTeam::where(['match_id' => $match_id,
        //     'user_id' => $user_id,])->get();
        return UserTeam::query()
            // Join for caption player details
            ->leftJoin('football_players as c', 'user_teams.caption_id', '=', 'c.player_id')
            ->leftJoin('football_team_players as cftp', function ($join) use ($season) {
                $join->on('cftp.player_id', '=', 'user_teams.caption_id')
                    ->where('cftp.season_id', '=', $season);
            })

            // Join for vice-caption player details
            ->leftJoin('football_players as vc', 'user_teams.voic_caption_id', '=', 'vc.player_id')
            ->leftJoin('football_team_players as vcftp', function ($join) use ($season) {
                $join->on('vcftp.player_id', '=', 'user_teams.voic_caption_id')
                    ->where('vcftp.season_id', '=', $season);
            })

            // You can remove this join since you're already getting caption and vc data from their joins
            // ->join('football_players', 'football_team_players.player_id', '=', 'football_players.player_id')

            ->where('user_teams.match_id', $match_id)
            ->where('user_teams.user_id', $user_id)
            ->select(
                'user_teams.*',

                // Caption info from football_players
                'c.player_id as caption_player_id',
                'c.common_name as caption_common_name',
                'c.firstname as caption_firstname',
                'c.lastname as caption_lastname',
                'c.name as caption_name',
                'c.display_name as caption_display_name',
                'c.image_path as caption_image_path',

                // Caption team from football_team_players
                'cftp.team_id as caption_team_id',

                // VC info from football_players
                'vc.player_id as vc_player_id',
                'vc.common_name as vc_common_name',
                'vc.firstname as vc_firstname',
                'vc.lastname as vc_lastname',
                'vc.name as vc_name',
                'vc.display_name as vc_display_name',
                'vc.image_path as vc_image_path',

                // VC team from football_team_players
                'vcftp.team_id as vc_team_id',
            )
            ->get();
    }
    public function mactchContestView(Request $request, $match_id, $contest_id)
    {
        $match = FootballMatch::withLeagueAndParticipants()->where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::EmptyReturn('Invalid match details');
        }
        $contest = FootballContest::where(['id' => $contest_id, 'match_id' => $match_id])->active()->with('prizeBreakups')->first();
        if (!$contest) {
            return Helper::EmptyReturn('Invalid contest details');
        }
        $joined_contest_teams = FootballJoinContest::where(['contest_id' => $contest_id, 'match_id' => $match_id])->orderby('ranks', 'asc')->with('user', 'team')->get();
        return Helper::SuccessReturn([
            'matchData' => $match,
            'contest' => $contest,
            'joined_contest_teams' => $joined_contest_teams,
        ], 'data fatched');
    }
    public function mymatches(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $joinedMatches = FootballJoinContest::where('user_id', $user_id)
            ->pluck('match_id')
            ->unique();
        $joinedContests = FootballJoinContest::where('user_id', $user_id)
            ->pluck('contest_id')
            ->unique();
        $matchQuery = FootballMatch::whereIn('match_id', $joinedMatches)->withLeagueAndParticipants();
        $livematch = (clone $matchQuery)->where('is_live', 1)->get();
        $upcomming = (clone $matchQuery)->where('is_upcomming',true)->take(10)->get();
        $otherMatch = (clone $matchQuery)->where(['is_live' => false])->whereDate('starting_at', '<', Carbon::today())->take(10)->get();
        return Helper::SuccessReturn([
            'upcoming' => $upcomming,
            'live' => $livematch,
            'other' => $otherMatch
        ], 'data fatched.');
    }
    public function getScore(Request $request,$match_id){
        $match = FootballMatch::where('match_id', $match_id)->first(); // withLeagueAndParticipants()
        if (!$match) {
            return Helper::EmptyReturn('Invalid match details');
        }
        $scores = FootballScores::where('match_id',$match_id)->get();
        return Helper::SuccessReturn($scores,'Scores fatched');
    }
}
