<?php

namespace App\Jobs;

use App\Models\Contest;
use App\Models\Fixture;
use App\Models\JoinCrickContest;
use App\Models\Transection;
use App\Models\UserWallet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PrizeDistribute implements ShouldQueue
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
            $fixtures = Fixture::where(['is_completed' => true, 'is_prize_distributed' => false])->get();
            // $fixtures = Fixture::where('fixture_id',65546)->get();
            foreach ($fixtures as  $match) 
            {
                $contests = Contest::where(['match_id' => $match->fixture_id, 'is_cancelled' => false])->with('prizeBreakups')->get();

                foreach ($contests as $contest) 
                {
                    if (isset($contest->prizeBreakups)) 
                    {
                        $lastRankUpto = $contest->prizeBreakups->last()->rank_upto;
                        $JoindContest = JoinCrickContest::where(['match_id' => $match->fixture_id, 'contest_id' => $contest->id])
                            ->with('user')
                            ->where('ranks', '<=', $lastRankUpto)
                            ->orderBy('ranks', 'asc')
                            ->get();

                        foreach ($JoindContest as $key => $data) 
                        {
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

            Log::info([
                'Job' => 'PrizeDistribute',
                'Message' => 'Prize Distributed Successfully',
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'Job' => 'PrizeDistribute',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
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
}
