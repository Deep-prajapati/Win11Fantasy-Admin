<?php

namespace App\Jobs;

use App\Models\Fixture;
use App\Models\JoinCrickContest;
use App\Models\Playerspoint;
use App\Models\UserTeam;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SetPointsRank implements ShouldQueue
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
            // $matches = Fixture::where('fixture_id',62242)->pluck('fixture_id');
            $livematchs = Fixture::where('is_live', true)
                ->select('fixture_id', 'status', 'localteam_id', 'visitorteam_id')
                ->addSelect('is_live', 'is_cancelled', 'is_completed')
                ->get();
            foreach ($livematchs as $match) {

                $createdTeams = JoinCrickContest::where('match_id', $match->fixture_id)->pluck('created_team_id');
                $usersTeams =  UserTeam::where('match_id', $match->fixture_id)->whereIn('id', $createdTeams)->get();
                // return Playerspoint::where('fixture_id', 62242)->get();
                foreach ($usersTeams as $team) {
                    $main = Playerspoint::where('fixture_id', $match->fixture_id)
                        ->whereIn('player_id', $team->teams)
                        ->sum('points');
                    $captainPoints = Playerspoint::where('fixture_id', $match->fixture_id)
                        ->where('player_id', $team->caption_id)
                        ->sum('points');
                    $viceCaptainPoints = 0.5 * Playerspoint::where('fixture_id', $match->fixture_id)
                        ->where('player_id', $team->voic_caption_id)
                        ->sum('points');
                    $bonus = $captainPoints + $viceCaptainPoints;
                    $team->points = $main + $bonus;
                    $team->save();
                    JoinCrickContest::where(['match_id' => $match->fixture_id, 'created_team_id' => $team->id, 'user_id' => $team->user_id])->update([
                        'points' => $main + $bonus,
                    ]);
                }
                // if ($match->status == 'Finished') {
                //     $match->is_live = false;
                //     $match->is_completed = true;
                //     $match->update();
                // }
                // if ($match->status == "Aban.") {
                //     $match->is_live = false;
                //     $match->is_cancelled = true;
                //     $match->is_completed = false;
                //     $match->update();
                // }
            }

            Log::info([
                'Job' => 'SetPointsRank',
                'Message' => 'Data fatched successfully',
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'Job' => 'SetPointsRank',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}