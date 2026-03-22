<?php

namespace App\Jobs;

use App\Models\Contest;
use App\Models\Fixture;
use App\Models\JoinCrickContest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RankGenerate implements ShouldQueue
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
            $fixtures = Fixture::where('is_live', true)->get();
            // $fixtures = Fixture::where('fixture_id',65546)->get();

            foreach ($fixtures as $match) 
            {
                $contests = Contest::where(['match_id' => $match->fixture_id, 'is_cancelled' => false, 'is_active' => true])->pluck('id');

                foreach ($contests as $contest) 
                {
                    $JoindContest = JoinCrickContest::where(['match_id' => $match->fixture_id, 'contest_id' => $contest])
                        ->orderBy('points', 'desc')
                        ->pluck('points');

                    if (count($JoindContest) > 0) 
                    {
                        $pointsString = $JoindContest->implode(',');
                        $entries = DB::table('join_crick_contests')
                            ->select('id', 'user_id', 'created_team_id', 'points')
                            ->selectRaw("FIND_IN_SET(points, '$pointsString') as ranks")
                            ->where(['match_id' => $match->fixture_id, 'contest_id' => $contest])
                        ->orderBy('ranks', 'asc')->get();

                        foreach ($entries as $entry) 
                        {
                            JoinCrickContest::where('id', $entry->id)->update(['ranks' => $entry->ranks]);
                        }
                    }

                    if ($match->status == "Finished") 
                    {
                        $match->is_live = false;
                        $match->is_completed = true;
                        $match->update();
                    }

                    if ($match->status == "Aban.") 
                    {
                        $match->is_live = false;
                        $match->is_cancelled = true;
                        $match->is_completed = false;
                        $match->update();
                    }
                }
            }

            Log::info([
                'Job' => 'RankGenerate',
                'Message' => 'Rank Generate successfully'
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'Job' => 'RankGenerate',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}
