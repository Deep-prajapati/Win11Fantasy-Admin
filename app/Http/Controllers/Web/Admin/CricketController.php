<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\League;
use App\Models\Season;
use App\Helpers\Helper;
use App\Models\Contest;
use App\Models\Fixture;
use App\Models\ContestType;
use App\Models\PrizeBreakup;
use Illuminate\Http\Request;
use App\Models\DefaultContest;
use Illuminate\Validation\Rule;
use App\Models\JoinCrickContest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CricketController extends Controller
{
    public function index(Request $request)
    {
        $title = "Cricket Matches";
        return view('cricket.matches.index', compact('title'));
    }

    public function leagues(Request $request)
    {
        $title = "Cricket Leagues";
        return view('cricket.leagues', compact('title'));
    }

    // public function cancelMatch(Request $request, $fixture_id)
    // {
    //     $match =  Fixture::where(['fixture_id' => $fixture_id, 'is_live' => false, 'is_cancelled' => false])->first();
    //     if (!$match) {
    //         flash()->error('Match not found.');
    //         return redirect()->route('admin.cricket.matches');
    //     }
    //     $match->is_cancelled = true;
    //     $match->update();
    //     flash()->success('Match cancelled successfully.');
    //     return redirect()->route('admin.cricket.matches');
    // }

    public function matchContests(Request $request, $fixture_id)
    {
        $match =  Fixture::where('fixture_id', $fixture_id)->first();
        if (!$match) {
            flash()->error('Match not found.');
            return redirect()->route('admin.cricket.matches');
        }
        $title = "Match Contests List";
        $match->load('league');
        $alreadyContest = Contest::where('match_id', $fixture_id)->pluck("default_contest_id");
        $moreContests = DefaultContest::where(['is_cloneable' => false])->whereNotin('id', $alreadyContest)->with('contestType')->get();
        return view('cricket.matches.contest_list', compact('title', 'match', 'moreContests'));
    }

    public function matchContestAddManual(Request $request, $fixture_id)
    {
        $match =  Fixture::where('fixture_id', $fixture_id)->first();
        if (!$match) {
            return Helper::FalseReturn('Invalid Match Id');
        }
        $rules = [
            'contests' => ['required', 'array'],
            'contests.*' => ['required', Rule::exists('default_contests', 'id')]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Helper::EmptyReturn($validator->errors()->first());
        }
        $defContests = DefaultContest::whereIn('id', $request->contests)
            ->where('is_deleted', '!=', 1)
            ->get();

        DB::transaction(function () use ($defContests, $fixture_id) {
            foreach ($defContests as $contest) {
                $contests[] = [
                    'match_id' => $fixture_id,
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
                    'deleted_at' => null
                ];
            }
            Contest::upsert($contests, ['match_id', 'default_contest_id'], [
                'total_winning_prize',
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
                'updated_at',
                'deleted_at'
            ]);
        });
        return Helper::SuccessReturn(null, 'Contest added successfully.');
    }

    public function matchContestView(Request $request, $fixture_id, $contest_id)
    {

        $match =  Fixture::where('fixture_id', $fixture_id)->first();
        if (!$match) {
            flash()->error('Match not found.');
            return redirect()->route('admin.cricket.matches');
        }

        $contest = Contest::where(['match_id' => $fixture_id, 'id' => $contest_id])->first();
        $title = "Match Contest View";
        $joinedUsers = JoinCrickContest::where(['match_id' => $fixture_id, 'contest_id' => $contest_id])->orderby('ranks')->with('user')->paginate(env('PER_PAGE_RECORDS', 10)); //'team'
        $totalEntryAmount = JoinCrickContest::where(['match_id' => $fixture_id, 'contest_id' => $contest_id])->whereHas('user', function ($query) {
            $query->where('role', 2);
        })->count() * $contest->entry_fees;
        $totalWinnings = JoinCrickContest::where(['match_id' => $fixture_id, 'contest_id' => $contest_id])->whereHas('user', function ($query) {
            $query->where('role', 2);
        })->sum('winning_amount');

        return view('cricket.matches.contest_view', compact('title', 'match', 'contest', 'joinedUsers', 'totalWinnings', 'totalEntryAmount'));
    }

    public function getseasons(Request $request)
    {
        if (isset($request->league)) {
            $seasons = Season::where('league_id', $request->league)->get();
        } else {
            $seasons = Season::get();
        }
        return Helper::SuccessReturn($seasons, 'data fatched');
    }

    public function defaultContest(Request $request)
    {
        $title = "Default Contest";
        $contests = DefaultContest::query();

        $contests->with('contestType'); //'prizeBreakup'
        $contests = $contests->paginate(env('PER_PAGE_RECORDS', 10));
        return view('cricket.contests.index', compact('title', 'contests'));
    }

    public function defaultContestView(Request $request, $contest_id)
    {
        $title = "Default Contest View";
        $contest = DefaultContest::where('id', $contest_id)->first();
        if (!$contest) {
            flash()->error('Invalid Contest details.');
            return redirect()->route('admin.cricket.default.contest.index');
        }
        $contest->load('contestType', 'prizeBreakup');
        // return $contest;
        $usercontests = Contest::query()
            ->where('default_contest_id', $contest->id)
            ->join('fixtures', 'fixtures.fixture_id', '=', 'contests.match_id');

        // Filter by Date
        if (!empty($request->date)) {
            $usercontests->whereDate('fixtures.starting_at', $request->date);
        }

        // Filter by Status
        if (!empty($request->status)) {
            switch ($request->status) {
                case 'upcomming':
                    $usercontests->where(function ($query) {
                        $query->where('fixtures.starting_at', '>', now())
                            ->orWhere(function ($q) {
                                $q->where('fixtures.live', false)
                                    ->where('fixtures.status', 'NS');
                            });
                    });
                    break;
                case 'live':
                    $usercontests->where('fixtures.live', true)
                        ->where('fixtures.status', 'Live')
                        ->whereNull('fixtures.winner_team_id');
                    break;
                case 'completed':
                    $usercontests->where(function ($query) {
                        $query->whereNotNull('fixtures.winner_team_id')
                            ->orWhere('fixtures.status', 'Finished');
                    });
                    break;
                case 'cancelled':
                    $usercontests->where('fixtures.status', 'Aban.');
                    break;
                default:
                    break;
            }
        }

        if (isset($request->match_id)) {
            $usercontests = $usercontests->where('contests.match_id', $request->match_id);
        }
        $usercontests = $usercontests->orderBy('fixtures.starting_at', 'desc') // Order by match starting_at
            ->select('contests.*', 'fixtures.fixture_id', 'fixtures.starting_at', 'fixtures.status as match_status') // Select only
            ->paginate(env('PER_PAGE_RECORDS', 10));
        return view('cricket.contests.view', compact('title', 'contest', 'usercontests'));
    }

    public function defaultContestEdit(Request $request, $contest_id)
    {
        $title = "Default Contest Edit";
        $contest = DefaultContest::where('id', $contest_id)->first();
        if (!$contest) {
            flash()->error('Invalid Contest details.');
            return redirect()->route('admin.cricket.default.contest.index');
        }
        if ($request->isMethod('POST')) {
            $rules = [
                'contest_type'     => 'required|integer',
                'mrp'             => 'required|numeric|min:0',
                'entry_fee'       => 'required|numeric|min:1',
                'usable_bonus'    => 'required|numeric|min:0|max:100',
                'first_price'     => 'required|numeric|min:1',
                'total_spots'     => 'required|integer|min:1',
                'total_bots'     => 'required|integer|min:0',
                'is_bonus_contest' => 'nullable|in:on',
                'is_cloneable' => 'nullable|in:on',
                'is_free' => 'nullable|in:on',
                'cancellation' => 'nullable|in:on',
                'from_rank'       => 'required|array',
                'to_rank'         => 'required|array',
                'prize'           => 'required|array',
                'from_rank.*'     => 'required|integer|min:1',
                'to_rank.*'       => 'required|integer|min:1',
                'prize.*'         => 'required|numeric|min:1',
            ];
            $request->validate($rules);
            if (
                count($request->from_rank) !== count($request->to_rank) ||
                count($request->to_rank) !== count($request->prize)
            ) {
                flash()->error('Prize breakups info incorrect.');
                return redirect()->back()->withInput();
            }
            $contest->update([
                'contest_type' => $request->contest_type,
                'mrp' => $request->mrp,
                'entry_fees' => $request->entry_fee,
                'total_spots' => $request->total_spots,
                'first_prize' => $request->first_price,
                'prize_percentage' => 90,
                'winner_percentage' => 50,
                'cancellation' => isset($request->cancellation) ? "Yes" : "No",
                'total_winning_prize' => array_sum($request->prize),
                'is_free' => isset($request->is_free),
                'is3x' => false,
                'extra_cash' => 0,
                'bonus_contest' => isset($request->is_bonus_contest),
                'is_cloneable' => isset($request->is_cloneable),
                'usable_bonus' => $request->usable_bonus,
                'bot_user' => $request->total_bots,
            ]);

            // Update Prize Breakup
            PrizeBreakup::where('default_contest_id', $contest->id)->delete(); // Remove old data

            foreach ($request->from_rank as $key => $value) {
                PrizeBreakup::create([
                    'default_contest_id' => $contest->id,
                    'contest_type_id' => $request->contest_type,
                    'rank_from' => $value,
                    'rank_upto' => $request->to_rank[$key],
                    'prize_amount' => $request->prize[$key],
                ]);
            }
            flash()->success('Contest updated successfully.');
            return redirect()->route('admin.cricket.default.contest.index');
        }
        $contest->load('prizeBreakup');
        $contest_types = ContestType::where('is_deleted', false)->get();
        return view('cricket.contests.edit', compact('title', 'contest', 'contest_types'));
    }

    public function defaultContestAdd(Request $request)
    {
        if ($request->isMethod('POST')) {
            $rules = [
                'contest_type'     => 'required|integer',
                'mrp'             => 'required|numeric|min:0',
                'entry_fee'       => 'required|numeric|min:1',
                'usable_bonus'    => 'required|numeric|min:0|max:100',
                'first_price'     => 'required|numeric|min:1',
                'total_spots'     => 'required|integer|min:1',
                'total_bots'     => 'required|integer|min:0',
                'is_bonus_contest' => 'nullable|in:on',
                'is_cloneable' => 'nullable|in:on',
                'is_free' => 'nullable|in:on',
                'cancellation' => 'nullable|in:on',
                'from_rank'       => 'required|array',
                'to_rank'         => 'required|array',
                'prize'           => 'required|array',
                'from_rank.*'     => 'required|integer|min:1',
                'to_rank.*'       => 'required|integer|min:1',
                'prize.*'         => 'required|numeric|min:1',
            ];
            $request->validate($rules);
            if (
                count($request->from_rank) !== count($request->to_rank) ||
                count($request->to_rank) !== count($request->prize)
            ) {
                flash()->error('prize breakups info incorrect.');
                return redirect()->back()->withInput();
            }
            $default_Contest = DefaultContest::create([
                'contest_type' => $request->contest_type,
                'mrp' => $request->mrp,
                'entry_fees' => $request->entry_fee,
                'total_spots' => $request->total_spots,
                'first_prize' => $request->first_price,
                'prize_percentage' => 90,
                'winner_percentage' => 50,
                'cancellation' => (isset($request->cancellation)) ? "Yes" : "No",
                'total_winning_prize' => array_sum($request->prize),
                'is_free' => (isset($request->is_free)) ? true : false,
                'is3x' => false,
                'extra_cash' => 0,
                'bonus_contest' => (isset($request->is_bonus_contest)) ? true : false,
                'is_cloneable' => (isset($request->is_cloneable)) ? true : false,
                'usable_bonus' => $request->usable_bonus,
                'bot_user' => $request->total_bots,
            ]);
            foreach ($request->from_rank as $key => $value) {
                PrizeBreakup::create([
                    'default_contest_id' => $default_Contest->id,
                    'contest_type_id' => $request->contest_type,
                    'rank_from' => $value,
                    'rank_upto' => $request->to_rank[$key],
                    'prize_amount' => $request->prize[$key],
                ]);
            }
            flash()->success('New Default contest added successfully.');
            return redirect()->route('admin.cricket.default.contest.index');
        } else {
            $title = "Default Contest add";
            $contest_types = ContestType::where('is_deleted', false)->get();
            return view('cricket.contests.add', compact('title', 'contest_types'));
        }
    }

    public function contestType(Request $request)
    {
        $title = "Contest Types";
        return view('cricket.contest_type.index', compact('title'));
    }

    public function contestTypeAdd(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate([
                'contest_type' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:220'],
                'max_entries' => ['required', 'numeric', 'min:1'],
            ]);
            ContestType::create([
                'contest_type' => $request->contest_type,
                'description' => $request->description,
                'max_entries' => $request->max_entries,
                'cancellable' => isset($request->cancellable) ? 'true' : 'false',
            ]);
            flash()->success('New Contest type Added successfully.');
            return redirect()->route('admin.cricket.contest.type.index');
        } else {
            $title = "Add Contest Type";
            return view('cricket.contest_type.add', compact('title'));
        }
    }
}