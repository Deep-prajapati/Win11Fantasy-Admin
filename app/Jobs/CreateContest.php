<?php

namespace App\Jobs;

use App\Models\Contest;
use App\Models\DefaultContest;
use App\Models\Fixture;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class CreateContest implements ShouldQueue
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
            $matches = Fixture::upcoming()->pluck('fixture_id');

            $defaultContests = DefaultContest::withTrashed()->where('is_cloneable', true)->get();
            $contests = [];

            DB::transaction(function () use ($defaultContests, $matches, $contests) 
            {
                foreach ($matches as $match_id) 
                {
                    foreach ($defaultContests as $contest) 
                    {
                        $contests[] = [
                            'match_id' => $match_id,
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
                        ];
                    }
                }
                
                Contest::upsert($contests, ['match_id', 'default_contest_id'], [
                    'total_winning_prize',
                    'contest_type',
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

            Log::info([
                'Job' => 'CreateContest',
                'Message' => 'Contest Created',
                'data' => [
                    'matches' => $matches
                ]
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'Job' => 'CreateContest',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}