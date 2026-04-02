<?php

namespace App\Jobs;

use App\Models\Batting;
use App\Models\Bowling;
use App\Models\Fixture;
use App\Services\SportsMonkService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetBatBall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $apiservice;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->apiservice = new SportsMonkService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // $matches = Fixture::where('is_live', true)->whereDate('starting_at', Carbon::now())->orderby('starting_at', 'asc')->pluck('fixture_id');
            // $matches = Fixture::where('fixture_id', 62242)->orderby('starting_at', 'asc')->pluck('fixture_id');
            // $details = $this->apiservice->getfixtureBettingBolling(65320);
            $matches = Fixture::where('is_live', true)->orderby('starting_at', 'asc')->pluck('fixture_id');
            foreach ($matches as $data) {
                $details = $this->apiservice->getfixtureBettingBolling($data);
                if ($details['success']) 
                {
                    $keysToRemove = ["updated_at", "resource", "id"];
                    $filteredBattingData = array_map(function ($item) use ($keysToRemove) {
                        return array_diff_key($item, array_flip($keysToRemove));
                    }, $details['data']['batting']);

                    // foreach ($filteredBattingData as $battingItem) {
                    //     Batting::updateOrCreate(
                    //         [
                    //             'fixture_id' => $battingItem['fixture_id'],
                    //             'team_id' => $battingItem['team_id'],
                    //             'player_id' => $battingItem['player_id'],
                    //             'scoreboard' => $battingItem['scoreboard']
                    //         ],
                    //         array_diff_key($battingItem, array_flip(['fixture_id', 'team_id', 'player_id', 'scoreboard']))
                    //     );
                    // }
                    $keysToRemove2 = ["updated_at", "resource", "id", 'sort'];
                    $filteredBollingData = array_map(function ($item) use ($keysToRemove2) {
                        return array_diff_key($item, array_flip($keysToRemove2));
                    }, $details['data']['bowling']);
                    DB::transaction(function () use ($filteredBattingData, $filteredBollingData) {
                        // Update batting records
                        foreach ($filteredBattingData as $battingItem) {
                            Batting::updateOrCreate(
                                [
                                    'fixture_id' => $battingItem['fixture_id'],
                                    'team_id' => $battingItem['team_id'],
                                    'player_id' => $battingItem['player_id'],
                                    'scoreboard' => $battingItem['scoreboard']
                                ],
                                array_diff_key($battingItem, ['fixture_id', 'team_id', 'player_id', 'scoreboard'])
                            );
                        }

                        foreach ($filteredBollingData as $bowlingItem) {
                            Bowling::updateOrCreate(
                                [
                                    'fixture_id' => $bowlingItem['fixture_id'],
                                    'team_id' => $bowlingItem['team_id'],
                                    'player_id' => $bowlingItem['player_id'],
                                    'scoreboard' => $bowlingItem['scoreboard']
                                ],
                                array_diff_key($bowlingItem, ['fixture_id', 'team_id', 'player_id', 'scoreboard'])
                            );
                        }
                    });
                }else{
                    Log::info([
                        'status' => 'error',
                        'Job' => 'GetBatBall',
                        'Message' => 'Failed to fatch data from api for fixture_id: ' . $data,
                    ]);
                }
            }

            Log::info([
                'status' => 'success',
                'Job' => 'GetBatBall',
                'Message' => 'bolling and betting records updated on ' . Carbon::now(),
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'status' => 'error',
                'Job' => 'GetBatBall',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}