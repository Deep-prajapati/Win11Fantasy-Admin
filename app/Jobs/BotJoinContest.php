<?php

namespace App\Jobs;

use App\Models\Contest;
use App\Models\Fixture;
use App\Models\JoinCrickContest;
use App\Models\Player;
use App\Models\User;
use App\Models\UserTeam;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BotJoinContest implements ShouldQueue
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
            $matches = Fixture::whereDate('starting_at', Carbon::today())
                ->whereNotIn('status', ['Aban.'])
                ->where('is_completed', false)
                ->where('is_cancelled', false)
                ->orderby('starting_at', 'asc')
                ->whereBetween('starting_at', [Carbon::now(), Carbon::now()->addMinutes(5)])
                // ->select('fixture_id', 'season_id')
                ->get();
            foreach ($matches as $match) {
                $contests = Contest::where('match_id', $match->fixture_id)
                    ->whereNotIn('contest_type', [2, 6])
                    ->with('contestType', 'defaultContest')
                    ->where(function ($query) {
                        $query->whereRaw('filled_spot < total_spots');
                    })
                    ->get();
                foreach ($contests as $contest) {
                    $this->joinBotUserInContest($match, $contest);
                }
                // $players = Player::where('season_id',$match->season_id)->whereIn('team_id',[$match->localteam_id,$match->visitorteam_id])->;
            }

            Log::info([
                'status' => 'success',
                'Job' => 'BotJoinContest',
                'Message' => 'Bot Join Contest Successfully',
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'status' => 'error',
                'Job' => 'BotJoinContest',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
        
    }

    private function JoinBotUserInContest($match, $contest)
    {
        while (botsAllowedInContest($contest->match_id, $contest, $contest->contestType)) {
            $this->joinbotToContest($match, $contest);
        }
    }

    private function joinbotToContest($match, $contest)
    {
        $botUser = User::where('role', 3)->inRandomOrder()->first();
        if (countBotUserJoinedInContestForMatch($contest->match_id, $contest->id, $botUser->id) < $contest->contestType->max_entries) {
            $this->createBotUserTeamAndJoin($match, $contest, $botUser);
        }
    }

    protected function createBotUserTeamAndJoin($match, $contest, $botUser)
    {
        $players = Player::where('season_id', $match->season_id)
            ->whereIn('team_id', [$match->localteam_id, $match->visitorteam_id])
            ->inRandomOrder()
            ->limit(11)
            ->pluck('player_id')
            ->toArray();

        if (count($players) < 11) {
            return null;
        }
        $captain = $players[array_rand($players)];
        $viceCaptain = $players[array_rand(array_diff($players, [$captain]))];
        $team =  UserTeam::create([
            'match_id' => $match->fixture_id,
            'user_id' => $botUser->id,
            'name' => $botUser->name,
            'team_id' => [$match->localteam_id, $match->visitorteam_id],
            'caption_id' => $captain,
            'voic_caption_id' => $viceCaptain,
            'teams' => $players,
        ]);
        $joined =  JoinCrickContest::create([
            'match_id' => $match->fixture_id,
            'user_id' => $botUser->id,
            'contest_id' => $contest->id,
            'created_team_id' => $team->id,
            'entryfee_bonus' => $contest->usable_bonus,
            'entryfee_deposit' => $contest->entry_fees,
        ]);
        if ($joined) {
            $contest->filled_spot++;
            $contest->update();
        }
        return true;
    }
}
