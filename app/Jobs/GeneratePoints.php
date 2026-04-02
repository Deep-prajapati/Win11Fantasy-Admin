<?php

namespace App\Jobs;

use App\Models\Fixture;
use App\Models\Playerspoint;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneratePoints implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // $livematchs =  Fixture::where('is_live', true)->whereDate('starting_at', Carbon::now())->select('fixture_id', 'localteam_id', 'visitorteam_id')->get();
            $livematchs =  Fixture::where('is_live', true)->select('fixture_id', 'localteam_id', 'visitorteam_id')->get();
            // $livematchs = Fixture::where('fixture_id', 62242)->orderby('starting_at', 'asc')->select('fixture_id', 'localteam_id', 'visitorteam_id')->get();
            foreach ($livematchs as $key => $match) 
            {
                if (count($match->playing11) > 0) 
                {
                    // $pointsbat = $this->battingPoints($match->fixture_id, $players);
                    //    return $pointsbowl = $this->bowlingPoints($match->fixture_id, $players);
                    $points = $this->getFantasyPoints($match->fixture_id, $match->playing11);

                    DB::transaction(function () use ($points, $match) 
                    {
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

            Log::info([
                'status' => 'success',
                'Job' => 'GeneratePoints',
                'Message' => 'Points generated successfully',
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'status' => 'error',
                'Job' => 'GeneratePoints',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
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
}