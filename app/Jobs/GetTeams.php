<?php

namespace App\Jobs;

use App\Models\Fixture;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Services\SportsMonkService;
use Illuminate\Support\Facades\Log;

class GetTeams implements ShouldQueue
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
            $matches = Fixture::select('localteam_id', 'fixture_id', 'visitorteam_id', 'season_id')
                ->whereDate('starting_at', '>=', Carbon::today())
                ->whereNot('status', 'Aban.')
                ->whereDate('starting_at', '<', Carbon::today()->addDays(5))
                ->where('is_completed', false)
            ->where('is_cancelled', false)->get();
            // ->whereIn('season_id',[6,24,44,185,309,312,498,507,782,1058,1292,1427,1648,1657,10,104,107,110,324,450,453,525,830,1079,1349,1624,15,188,191,648,986,1145,1496,1636])
            // ->skip(5)
            // ->take(5) 

            foreach ($matches as $match) 
            {
                $teamIds = [$match->localteam_id, $match->visitorteam_id];
                $playersData = [];

                foreach ($teamIds as $teamId) 
                {
                    // $teamDetails = $this->apiservice->getTeam($teamId); complete
                    $teamDetails = $this->apiservice->getteamSquad($teamId, $match->season_id);

                    if (!isset($teamDetails['data']['squad'])) 
                    {
                        continue; // Skip if no squad data
                    }

                    if (isset($teamDetails['data']['error'])) 
                    {
                        print_r($teamDetails['data']);
                        break; // Skip if no squad data
                    }

                    foreach ($teamDetails['data']['squad'] as $data) 
                    {
                        $playersData[] = [
                            'player_id' => $data['id'],
                            'team_id' => $teamId,
                            'season_id' => $match->season_id,
                            'country_id' => $data['country_id'],
                            'firstname' => $data['firstname'],
                            'lastname' => $data['lastname'],
                            'fullname' => $data['fullname'],
                            'image_path' => $data['image_path'],
                            'dateofbirth' => $data['dateofbirth'],
                            'gender' => $data['gender'],
                            'battingstyle' => $data['battingstyle'],
                            'bowlingstyle' => $data['bowlingstyle'],
                            'position_id' => $data['position']['id'],
                            'position_name' => $data['position']['name'],
                            'updated_at' => now(), // Ensure timestamps are updated
                            'credits' => getDefaultCredits($data['position']['id']),
                        ];
                    }
                }

                // **Bulk Insert or Update for Unique Constraint (player_id, team_id, match_id)**
                if (!empty($playersData)) 
                {
                    // return $playersData;
                    DB::transaction(function () use ($playersData) 
                    {
                        Player::upsert(
                            $playersData, // Data to insert/update
                            ['player_id', 'team_id', 'season_id'], // Unique key constraints
                            ['fullname', 'image_path', 'battingstyle', 'bowlingstyle', 'position_id', 'position_name', 'credits', 'updated_at'] // Columns to update if conflict occurs
                        );
                    });
                }
            }
            
            Log::info([
                'Job' => 'GetTeams',
                'Message' => 'Squad data fetched successfully'
            ]);
        } catch (\Throwable $th) {
            Log::info([
                'Job' => 'GetTeams',
                'Message' => 'Failed to fatch data',
                'data' => $th->getMessage()
            ]);
        }
    }
}