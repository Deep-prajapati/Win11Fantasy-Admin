<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Player;
use App\Helpers\Helper;
use App\Models\Batting;
use App\Models\Bowling;
use App\Models\Contest;
use App\Models\Fixture;
use App\Models\League;
use App\Models\UserTeam;
use App\Models\Playing11;
use App\Models\Transection;
use App\Models\PrizeBreakup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\JoinCrickContest;
use App\Http\Controllers\Controller;
use App\Models\Playerspoint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    public function index(Request $request, $status)
    {
        $matches = Fixture::query();
        if ($status == 'live') {
            $matches = $matches->live();
        } else if ($status == 'upcoming') {
        $matches = $matches->upcoming();
        } else {
            $matches = $matches->finished();
        }
        $notAllowedLeagues = League::where('status', false)->pluck('league_id');
        $matches = $matches->join('leagues', 'fixtures.league_id', '=', 'leagues.league_id');
        $matches =  $matches
        ->whereNotIn('fixtures.season_id', [1701])
        ->whereNotIn('fixtures.league_id', $notAllowedLeagues)
        ->where('fixtures.is_cancelled', false)
        ->select(
            'fixtures.localteam_name',
            'fixtures.localteam_code',
            'fixtures.localteam_image_path',
            'fixtures.visitorteam_name',
            'fixtures.visitorteam_code',
            'fixtures.visitorteam_image_path',
            'fixtures.starting_at',
            "fixtures.localteam_id",
            "fixtures.visitorteam_id",
            'fixtures.fixture_id',
            'fixtures.league_id',
            'fixtures.note',
            'fixtures.season_id',
            'fixtures.round',
            'fixtures.note',
            'fixtures.is_live',
            'fixtures.is_cancelled',
            'fixtures.is_completed',
            'leagues.name as league_name',
            'leagues.code as league_code'
        );
        $matches = $matches->with('teama', 'teamb')->take(20)->get();
        return Helper::SuccessReturn($matches, 'match list fatched.');
    }
    
    public function matchdetails(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        if (!$fixture) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
        $match =  Fixture::where('fixture_id', $fixture_id)
            ->join('leagues', 'fixtures.league_id', '=', 'leagues.league_id')
            ->select(
                'fixtures.localteam_name',
                'fixtures.localteam_code',
                'fixtures.localteam_image_path',
                'fixtures.visitorteam_name',
                'fixtures.visitorteam_code',
                'fixtures.visitorteam_image_path',
                'fixtures.starting_at',
                "fixtures.localteam_id",
                "fixtures.visitorteam_id",
                'fixtures.fixture_id',
                'fixtures.league_id',
                'fixtures.note',
                'fixtures.season_id',
                'fixtures.round',
                'fixtures.note',
                'fixtures.live',
                'fixtures.status',
                'fixtures.is_live',
                'fixtures.is_cancelled',
                'fixtures.is_completed',
                'leagues.name as league_name',
                'leagues.code as league_code'
            )->with('teama', 'teamb','battings','bowlings')
            ->first();
        return Helper::SuccessReturn($match, 'match data fatched.');
    }

    public function players(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        if (!$fixture) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
        $data['teams'] = [$fixture->localteam_id, $fixture->visitorteam_id];
        $data['players'] =  Player::whereIn('players.team_id', [$fixture->localteam_id, $fixture->visitorteam_id])
            ->join('teams', 'teams.team_id', '=', 'players.team_id')
            ->select(
                'players.player_id',
                'players.fullname',
                'players.image_path',
                'players.battingstyle',
                'players.bowlingstyle',
                'players.position_id',
                'players.position_name',
                'teams.name as team_name',
                'teams.code as team_code',
                'teams.team_id as team_id',
            )
            ->get();
        return Helper::SuccessReturn($data, 'match list fatched.');
        $data['playing11']['a'] = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->localteam_id])->pluck('player_id');
        $data['playing11']['b'] = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->visitorteam_id])->pluck('player_id');
    }

    public function playing11(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();

        if (!$fixture) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }

        $data['playing11']['a'] = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->localteam_id])->pluck('player_id');
        $data['playing11']['b'] = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->visitorteam_id])->pluck('player_id');

        return Helper::SuccessReturn($data, 'data list fatched.');
    }

    public function contests(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        if (!$fixture) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
        $contests = Contest::where('match_id', $fixture_id)->active()
            ->select('id', 'mrp', 'contest_type', 'total_winning_prize', 'entry_fees', 'total_spots', 'filled_spot', 'default_contest_id', 'contest_type', 'first_prize', 'is_free', 'usable_bonus', 'is_felexible')
            ->with(['contestType:id,contest_type,max_entries,cancellable as is_temp', 'prizeBreakups'])
            ->orderby('total_winning_prize','desc')
            ->get();
        $user = Auth::guard('api')->user(); // or just Auth::user() if default guard is 'api'
        if ($user) {
            $userContests = JoinCrickContest::where([
                'match_id' => $fixture_id,
                'user_id' => $user->id
            ])
                ->get()
                ->groupBy('contest_id') // Group by contest_id
                ->map(function ($group) {
                    $teamIds = $group->pluck('created_team_id')->implode(',');
                    $firstItem = $group->first();
                    $firstItem->teams = $teamIds;
                    unset($firstItem['created_team_id']);
                    return $firstItem;
                })
                ->values();
        }else{
            $userContests = [];
        }
        return Helper::SuccessReturn(['contests' => $contests, 'userContests' => $userContests], 'contest list fatched.');
    }

    public function priceDetails(Request $request, $contest_id)
    {
        $contest = Contest::active()->where('id', $contest_id)->first();
        if (!$contest) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
        $pricedetails = PrizeBreakup::where(['contest_type_id' => $contest->contest_type, 'default_contest_id' => $contest->default_contest_id])->get();
        return $pricedetails;
    }

    public function createTeam(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();

        if (!$fixture) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }

        if (($fixture->is_live == true || $fixture->is_cancelled == true || $fixture->is_completed == true) || Carbon::now()->gte(Carbon::parse($fixture->starting_at)->subMinute())) 
        {
            return Helper::FalseReturn(null, 'You cannot create team now. Match already live.');
        }

        $team_id = [$fixture->localteam_id, $fixture->visitorteam_id];

        $rules = [
            'players' => ['required', 'array', 'size:11'],
            'players.*' => [Rule::exists('players', 'player_id')->where(function ($query) use ($team_id) {
                $query->whereIn('team_id', $team_id);
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
            return Helper::FalseReturn(null, $validator->errors()->first());
        }

        $user = auth()->user();

        if ($request->c_player == $request->vc_player) 
        {
            return Helper::FalseReturn(null, 'Caption and voice caption player cannot be one player.');
        }

        $teama = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->localteam_id])->pluck('player_id')->toArray();
        $teamb = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->visitorteam_id])->pluck('player_id')->toArray();

        if (count(array_intersect($request->players, $teama)) > 7 || count(array_intersect($request->players, $teamb)) > 7) 
        {
            return Helper::FalseReturn(null, 'Max 7 player allowed from single team.');
        }

        UserTeam::create([
            'match_id' => $fixture_id,
            'user_id' => $user->id,
            'name' => $user->name,
            'team_id' => $team_id,
            'caption_id' => $request->c_player,
            'voic_caption_id' => $request->vc_player,
            'teams' => $request->players,
        ]);

        return Helper::SuccessReturn('', 'Team created successfully.');
    }

    public function updateTeam(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        
        if (!$fixture) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }

        if (($fixture->is_live == true || $fixture->is_cancelled == true || $fixture->is_completed == true) || Carbon::now()->gte(Carbon::parse($fixture->starting_at)->subMinute())) 
        {
            return Helper::FalseReturn(null, 'You cannot update team now.');
        }

        $user = auth()->user();

        $team_id = [$fixture->localteam_id, $fixture->visitorteam_id];

        $rules = [
            'team_id' => ['required', Rule::exists('user_teams', 'id')],
            'players' => ['required', 'array', 'size:11'],
            'players.*' => [Rule::exists('players', 'player_id')->where(function ($query) use ($team_id) {
                $query->whereIn('team_id', $team_id);
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

        if ($validator->fails()) 
        {
            return Helper::FalseReturn(null, $validator->errors()->first());
        }

        if ($request->c_player == $request->vc_player) 
        {
            return Helper::FalseReturn(null, 'Caption and voice caption player cannot be one player.');
        }

        $teama = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->localteam_id])->pluck('player_id')->toArray();
        $teamb = Playing11::where(['fixture_id' => $fixture_id, 'team_id' => $fixture->visitorteam_id])->pluck('player_id')->toArray();

        if (count(array_intersect($request->players, $teama)) > 7 || count(array_intersect($request->players, $teamb)) > 7) 
        {
            return Helper::FalseReturn(null, 'Max 7 player allowed from single team.');
        }

        $team = UserTeam::where('id', $request->team_id)->first();

        $team->update([
            'match_id' => $fixture_id,
            'user_id' => $user->id,
            'name' => $user->name,
            'team_id' => $team_id,
            'caption_id' => $request->c_player,
            'voic_caption_id' => $request->vc_player,
            'edit_count' => $team->edit_count + 1,
            'teams' => $request->players,
        ]);

        return Helper::SuccessReturn('', 'Team updated successfully.');
    }

    public function getTeam(Request $request, $fixture_id)
    {
        // Log::channel('fixture')->info('Request header', $request->header());
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        if (!$fixture) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
        $user = auth()->user();
        $teams = UserTeam::withCaptionsImg()->where(['match_id' => $fixture_id, 'user_id' => $user->id])->get();
        return Helper::SuccessReturn($teams, 'Team list fatched for current match.');
    }

    public function getScore(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        if (!$fixture) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
        $scores['batting'] = Batting::where('fixture_id', $fixture_id)->get();
        $scores['bowling'] = Bowling::where('fixture_id', $fixture_id)->get();
        return Helper::SuccessReturn($scores, 'data fatched');
    }

    public function preview(Request $request, $fixture_id, $team_id)
    {
        $user = auth()->user();

        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        
        if (!$fixture) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }

        $team = UserTeam::where([
            'match_id' => $fixture_id,
            'id' => $team_id
        ])->first();

        $team->players = $fixture->players($team->teams);
        
        $points = Playerspoint::where(['fixture_id' => $fixture_id])->whereIn('player_id', $team->teams)->pluck('points', 'player_id');

        $data = [
            'team' => $team,
            'player_point' => $points,
        ];
        
        return Helper::SuccessReturn($data, 'Team fatched.');
    }

    public function joinedContest(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();
        if (!$fixture) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
        $user = auth()->user();
        $data = JoinCrickContest::where(['user_id' => $user->id, 'match_id' => $fixture_id])->get();
        return Helper::SuccessReturn($data, 'data fatched');
    }

    public function addJoinContest(Request $request, $fixture_id)
    {
        $fixture = Fixture::where('fixture_id', $fixture_id)->first();

        if (!$fixture) 
        {
            return Helper::EmptyReturn('Invalid details');
        }

        $rules = [
            'team_id' => ['required', Rule::exists('user_teams', 'id')],
            'contest_id' => ['required', Rule::exists('contests', 'id')->where('is_active', true)],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) 
        {
            return Helper::EmptyReturn($validator->errors()->first());
        }

        $contest = Contest::where('id', $request->contest_id)->active()->with('defaultContest', 'contestType', 'prizeBreakups')->first();

        if ($contest->is_cancelled) 
        {
            return Helper::EmptyReturn('Contest cancelled.');
        }

        if ($contest->filled_spot == $contest->total_spots) 
        {
            return Helper::EmptyReturn('Contest already filled.');
        }

        $user = auth()->user();

        if ($contest->contestType->max_entries == joinedCricTeamCount($user->id, $fixture_id, $request->contest_id)) 
        {
            return Helper::EmptyReturn('Already used max allowed team.');
        }

        if (alreayJoinedContestWithTeam($user->id, $request->team_id, $fixture_id, $request->contest_id)) 
        {
            return Helper::EmptyReturn('Already joined this contest using this team.');
        }
        
        $winning = 0;
        $balance = 0;
        $bonus = 0;

        if (!$contest->defaultContest->is_free) 
        {
            $user->load('account');

            if($contest->usable_bonus > 0 && $user->account->bonus > 0)
            {
                if($user->account->bonus >= $contest->usable_bonus)
                {
                    if (($contest->entry_fees - $contest->usable_bonus) > ($user->account->balance + $user->account->winning)) 
                    {
                        return Helper::EmptyReturn('Insufficient balance. Please recharge first.');
                    }

                    if ($user->account->balance < ($contest->entry_fees - $contest->usable_bonus)) 
                    {
                        $amountFromWinnings = ($contest->entry_fees - $contest->usable_bonus) - $user->account->balance;

                        $user->account->winning -= $amountFromWinnings;
                        $winning = $amountFromWinnings;

                        $balance = $user->account->balance;
                        $user->account->balance = 0;

                        $user->account->bonus -= $contest->usable_bonus;
                        $bonus = $contest->usable_bonus;
                    } else {
                        $user->account->balance -= ($contest->entry_fees - $contest->usable_bonus);
                        $balance = ($contest->entry_fees - $contest->usable_bonus);

                        $user->account->bonus -= $contest->usable_bonus;
                        $bonus = $contest->usable_bonus;
                    }
                }else{
                    if (($contest->entry_fees - $user->account->bonus) > ($user->account->balance + $user->account->winning)) 
                    {
                        return Helper::EmptyReturn('Insufficient balance. Please recharge first.');
                    }

                    if ($user->account->balance < ($contest->entry_fees - $user->account->bonus)) 
                    {
                        $amountFromWinnings = ($contest->entry_fees - $user->account->bonus) - $user->account->balance;

                        $user->account->winning -= $amountFromWinnings;
                        $winning = $amountFromWinnings;

                        $balance = $user->account->balance;
                        $user->account->balance = 0;

                        $bonus = $user->account->bonus;
                        $user->account->bonus = 0;
                    } else {
                        $user->account->balance -= ($contest->entry_fees - $user->account->bonus);
                        $balance = ($contest->entry_fees - $user->account->bonus);

                        $bonus = $user->account->bonus;
                        $user->account->bonus = 0;
                    }
                }
            }
            else
            {
                if ($contest->entry_fees > ($user->account->balance + $user->account->winning)) 
                {
                    return Helper::EmptyReturn('Insufficient balance. Please recharge first.');
                }

                if ($user->account->balance < $contest->entry_fees) 
                {
                    $amountFromWinnings = $contest->entry_fees - $user->account->balance;

                    $user->account->winning -= $amountFromWinnings;
                    $winning = $amountFromWinnings;

                    $balance = $user->account->balance;
                    $user->account->balance = 0;
                } else {
                    $user->account->balance -= $contest->entry_fees;
                    $balance = $contest->entry_fees;
                }
            }

            $user->account->save();

            Transection::create([
                'user_id' => $user->id,
                'type' => 2,
                'amount' => $contest->entry_fees,
                'desc' => 'Contest Entry | ' . $fixture->localteam_code . ' - ' . $fixture->visitorteam_code,
            ]);
        }

        $data =  JoinCrickContest::create([
            'match_id' => $fixture_id,
            'user_id' => $user->id,
            'contest_id' => $request->contest_id,
            'created_team_id' => $request->team_id,
            'entryfee_winning' => $winning,
            'entryfee_bonus' => $bonus,
            'entryfee_deposit' => $balance,
        ]);

        $contest->filled_spot += 1;
        $contest->save();

        return Helper::SuccessReturn($data, 'Contest joined successfully.');
    }

    public function mymatches(Request $request)
    {
        $user = auth()->user();
        $joinedMatches = JoinCrickContest::where('user_id', $user->id)
            ->pluck('match_id')
            ->unique();
        $joinedContests = JoinCrickContest::where('user_id', $user->id)
            ->pluck('contest_id')
            ->unique();
        $fixtures = Fixture::whereIn('fixture_id', $joinedMatches)
            ->orderby('fixtures.starting_at', 'desc')
            ->join('leagues', 'fixtures.league_id', '=', 'leagues.league_id')
            ->select(
                'fixtures.localteam_name',
                'fixtures.localteam_code',
                'fixtures.localteam_image_path',
                'fixtures.visitorteam_name',
                'fixtures.visitorteam_code',
                'fixtures.visitorteam_image_path',
                'fixtures.starting_at',
                'fixtures.localteam_id',
                'fixtures.visitorteam_id',
                'fixtures.fixture_id',
                'fixtures.league_id',
                'fixtures.note',
                'fixtures.season_id',
                'fixtures.round',
                'fixtures.note',
                'fixtures.live',
                'fixtures.status',
                'fixtures.is_live',
                'fixtures.is_cancelled',
                'fixtures.is_completed',
                'leagues.name as league_name',
                'leagues.code as league_code'
            )
            ->with([
                'contests' => function ($query) use ($joinedContests, $user) {
                    $query->whereIn('id', $joinedContests)
                        ->with('contestType', 'prizeBreakups')
                        ->with(['userJoinedContests' => function ($joinQuery) use ($user) {
                            $joinQuery->where('user_id', $user->id)
                                ->get();
                            // ->groupBy('contest_id')
                            // ->map(function ($group) {
                            //     $teamIds = $group->pluck('created_team_id')->implode(',');
                            //     $firstItem = $group->first();
                            //     $firstItem->teams = $teamIds;
                            //     unset($firstItem['created_team_id']);
                            //     // unset($firstItem['id']);
                            //     return $firstItem;
                            // });
                        }]);
                }
            ])
            ->get();

        return Helper::SuccessReturn($fixtures, 'data fatched.');
    }

    public function mactchContestView(Request $request, $fixture_id, $contest_id)
    {
        $matchData = Fixture::where('fixture_id', $fixture_id)
        ->orderby('fixtures.starting_at', 'asc')
        ->join('leagues', 'fixtures.league_id', '=', 'leagues.league_id')
        ->select(
            'fixtures.localteam_name',
            'fixtures.localteam_code',
            'fixtures.localteam_image_path',
            'fixtures.visitorteam_name',
            'fixtures.visitorteam_code',
            'fixtures.visitorteam_image_path',
            'fixtures.starting_at',
            'fixtures.localteam_id',
            'fixtures.visitorteam_id',
            'fixtures.fixture_id',
            'fixtures.league_id',
            'fixtures.note',
            'fixtures.season_id',
            'fixtures.round',
            'fixtures.note',
            'fixtures.live',
            'fixtures.status',
            'fixtures.is_live',
            'fixtures.is_cancelled',
            'fixtures.is_completed',
            'leagues.name as league_name',
            'leagues.code as league_code'
        )->first();

        if (!$matchData) 
        {
            return Helper::EmptyReturn('Invalid match details');
        }

        $contest = Contest::where(['id' => $contest_id, 'match_id' => $fixture_id])->active()->with('contestType', 'defaultContest', 'prizeBreakups')->first();

        if (!$contest) 
        {
            return Helper::EmptyReturn('Invalid contest details');
        }

        $joined_contest_teams = JoinCrickContest::where(['contest_id' => $contest_id, 'match_id' => $fixture_id])->orderby('ranks', 'asc')->with('user', 'team')->get();

        return Helper::SuccessReturn([
            'matchData' => $matchData,
            'contest' => $contest,
            'joined_contest_teams' => $joined_contest_teams,
        ], 'data fatched');
    }
}