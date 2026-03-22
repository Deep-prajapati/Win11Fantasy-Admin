<?php

namespace App\Http\Controllers\Web\Admin\Football;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\FootballContest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\FootballDefaultContest;
use App\Models\FootballJoinContest;
use Illuminate\Support\Facades\Validator;

class MatchesController extends Controller
{
    public function index()
    {
        $title = 'Football Matches';
        return view('football.matches.index', compact('title'));
    }
    public function leagues(Request $request)
    {
        $title = "Football Leagues";
        return view('football.leagues', compact('title'));
    }
    public function matchContests(Request $request, $match_id)
    {
        $match =  FootballMatch::withLeagueAndParticipants()->where('match_id', $match_id)->first();
        if (!$match) {
            flash()->error('Match not found.');
            return redirect()->route('admin.football.matches');
        }
       $title = "Match Contests List";
        $alreadyContest = FootballContest::where('match_id', $match_id)->pluck("default_contest_id");
        $moreContests = FootballDefaultContest::where(['is_cloneable' => false])->whereNotin('id', $alreadyContest)->get();
        return view('football.matches.contest_list', compact('title', 'match', 'moreContests'));
    }
    public function matchContestAddManual(Request $request, $match_id)
    {
        $match =  FootballMatch::withLeagueAndParticipants()->where('match_id', $match_id)->first();
        if (!$match) {
            return Helper::FalseReturn('Invalid Match Id');
        }
        $rules = [
            'contests' => ['required', 'array'],
            'contests.*' => ['required', Rule::exists('football_default_contests', 'id')]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Helper::EmptyReturn($validator->errors()->first());
        }
        $defContests = FootballDefaultContest::withTrashed()->whereIn('id', $request->contests)->get();
        DB::transaction(function () use ($defContests, $match_id) {
            $contests = [];

            foreach ($defContests as $contest) {
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
                    'updated_at' => now(),
                ];
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
        return Helper::SuccessReturn(null,'Contest added successfully.');
    }
    public function matchContestView(Request $request, $match_id, $contest_id){
        $match =  FootballMatch::withLeagueAndParticipants()->where('match_id', $match_id)->first();
        if (!$match) {
            flash()->error('Match not found.');
            return redirect()->route('admin.cricket.matches');
        }
        $contest = FootballContest::where(['match_id' => $match_id, 'id' => $contest_id])->first();
        $title = "Match Contest View";
        $joinedUsers = FootballJoinContest::where(['match_id' => $match_id, 'contest_id' => $contest_id])->orderby('ranks')->with('user')->paginate(env('PER_PAGE_RECORDS', 10)); //'team'
        $totalEntryAmount = FootballJoinContest::where(['match_id' => $match_id, 'contest_id' => $contest_id])->whereHas('user', function ($query) {
            $query->where('role', 2);
        })->count() * $contest->entry_fees;
        $totalWinnings = FootballJoinContest::where(['match_id' => $match_id, 'contest_id' => $contest_id])->whereHas('user', function ($query) {
            $query->where('role', 2);
        })->sum('winning_amount');

        return view('football.matches.contest_view', compact('title', 'match', 'contest', 'joinedUsers', 'totalWinnings', 'totalEntryAmount'));
    }
}
