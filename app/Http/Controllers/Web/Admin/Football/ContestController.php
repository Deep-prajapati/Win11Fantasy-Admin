<?php

namespace App\Http\Controllers\Web\Admin\Football;

use App\Http\Controllers\Controller;
use App\Models\FootballContest;
use App\Models\FootballContestType;
use App\Models\FootballDefaultContest;
use App\Models\FootballPrizeBreakup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContestController extends Controller
{
    public function defaultContest(Request $request)
    {
        $title = "Default Contest";
        return view('football.contests.index', compact('title'));
    }
    public function defaultContestAdd(Request $request)
    {
        if ($request->isMethod('POST')) {
            $rules = [
                'contest_type' => ['required', Rule::exists('football_contest_types', 'id')->where(function ($query) {
                    $query->where('status', true);
                }),],
                'mrp'             => 'required|numeric|min:0',
                'max_entries' => 'required|numeric|min:1',
                'entry_fee'       => 'required|numeric|min:1',
                'usable_bonus'    => 'required|numeric|min:0|max:100',
                // 'first_price'     => 'required|numeric|min:1',
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
            $Findex = array_search(1, $request->from_rank);
            if ($Findex !== false && isset($request->prize[$Findex])) {
                $firstPrize = $request->prize[$Findex];
            } else {
                flash()->error('prize for first rank not fount.');
                return redirect()->back()->withInput();
            }
            $type = FootballContestType::where(['id' => $request->contest_type, 'status' => true])->first();
            if (!$type) {
                flash()->error('invalid contest type.');
                return redirect()->back()->withInput();
            }
            $default_Contest = FootballDefaultContest::create([
                'mrp' => $request->mrp,
                'contest_type_id' => $request->contest_type,
                'contest_type' => $type->name,
                'contest_type_code' => $type->code,
                'max_entries' => $request->max_entries,
                'entry_fees' => $request->entry_fee,
                'total_spots' => $request->total_spots,
                'first_prize' => $firstPrize,
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
                FootballPrizeBreakup::create([
                    'default_contest_id' => $default_Contest->id,
                    'rank_from' => $value,
                    'rank_upto' => $request->to_rank[$key],
                    'prize_amount' => $request->prize[$key],
                ]);
            }
            flash()->success('New Default contest added successfully.');
            return redirect()->route('admin.football.default.contest.index');
        } else {
            $title = "Default Contest add";
            $contest_types = FootballContestType::where('status', true)->get();
            return view('football.contests.add', compact('title', 'contest_types'));
        }
    }

    public function defaultContestEdit(Request $request, $contest_id)
    {
        $title = "Default Contest Edit";
        $contest = FootballDefaultContest::where('id', $contest_id)->first();
        if (!$contest) {
            flash()->error('Invalid Contest details.');
            return redirect()->route('admin.football.default.contest.index');
        }
        if ($request->isMethod('POST')) {
            $rules = [
                'contest_type' => ['required', Rule::exists('football_contest_types', 'id')->where(function ($query) {
                    $query->where('status', true);
                }),],
                'mrp'             => 'required|numeric|min:0',
                'max_entries' => 'required|numeric|min:1',
                'entry_fee'       => 'required|numeric|min:1',
                'usable_bonus'    => 'required|numeric|min:0|max:100',
                // 'first_price'     => 'required|numeric|min:1',
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
            $Findex = array_search(1, $request->from_rank);
            if ($Findex !== false && isset($request->prize[$Findex])) {
                $firstPrize = $request->prize[$Findex];
            } else {
                flash()->error('prize for first rank not fount.');
                return redirect()->back()->withInput();
            }
            $type = FootballContestType::where(['id' => $request->contest_type, 'status' => true])->first();
            if (!$type) {
                flash()->error('invalid contest type.');
                return redirect()->back()->withInput();
            }
            $contest->update([
                'contest_type_id' => $request->contest_type,
                'contest_type' => $type->name,
                'contest_type_code' => $type->code,
                'mrp' => $request->mrp,
                'max_entries' => $request->max_entries,
                'entry_fees' => $request->entry_fee,
                'total_spots' => $request->total_spots,
                'first_prize' => $firstPrize,
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
            FootballPrizeBreakup::where('default_contest_id', $contest->id)->delete(); // Remove old data

            foreach ($request->from_rank as $key => $value) {
                FootballPrizeBreakup::create([
                    'default_contest_id' => $contest->id,
                    'rank_from' => $value,
                    'rank_upto' => $request->to_rank[$key],
                    'prize_amount' => $request->prize[$key],
                ]);
            }
            flash()->success('Contest updated successfully.');
            return redirect()->route('admin.football.default.contest.index');
        }
        $contest->load('prizeBreakup');
        $contest_types = FootballContestType::where('status', true)->get();
        return view('football.contests.edit', compact('title', 'contest_types', 'contest'));
    }
    public function defaultContestView(Request $request, $contest_id)
    {
        $title = "Default Contest View";
        $contest = FootballDefaultContest::where('id', $contest_id)->first();
        if (!$contest) {
            flash()->error('Invalid Contest details.');
            return redirect()->route('admin.football.default.contest.index');
        }
        $contest->load('prizeBreakup');
        $usercontests = [];
        $usercontests = FootballContest::query()
            ->where('default_contest_id', $contest->id)
            ->join('football_matches', 'football_matches.match_id', '=', 'football_contests.match_id');

        if (!empty($request->date)) {
            $usercontests->whereDate('football_matches.starting_at', $request->date);
        }
        // if (isset($request->match_id)) {
        //     $usercontests = $usercontests->where('contests.match_id', $request->match_id);
        // }
        $usercontests = $usercontests->orderBy('football_matches.starting_at', 'desc') // Order by match starting_at
            ->select(
                'football_contests.*',
                'football_matches.match_id',
                'football_matches.starting_at',
                'football_matches.is_upcomming',
                'football_matches.is_live',
                'football_matches.is_completed',
                'football_matches.is_cancelled',
            ) // Select only
            ->paginate(env('PER_PAGE_RECORDS', 10));
        return view('football.contests.view', compact('title', 'contest', 'usercontests'));
    }

    public function contestType(Request $request)
    {
        $title = 'Contest Type';
        $contestTypes = FootballContestType::orderBy('status', 'desc')->paginate(env('PER_PAGE_RECORDS', 10));
        return view('football.contest-type.index', compact('title', 'contestTypes'));
    }
    public function contestTypeAdd(Request $request)
    {
        if ($request->isMethod('POST')) {
            $rules = [
                'contest_type' => ['required', 'string', 'max:150'],
                'short_code' => ['required', 'string', 'max:50'],
            ];
            $request->validate($rules);
            $type = new  FootballContestType();
            $type->name = $request->contest_type;
            $type->code = $request->short_code;
            $type->save();
            flash()->success('Contest type added successfully.');
            return redirect()->route('admin.football.contest.type.index');
        } else {
            $title = 'Add Contest Type';
            return view('football.contest-type.add', compact('title'));
        }
    }
}
