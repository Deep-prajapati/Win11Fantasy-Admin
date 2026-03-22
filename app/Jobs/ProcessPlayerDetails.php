<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FootballSeason;
use App\Models\FootballTeamPlayers;
use App\Models\FootballPlayers;
use App\Services\FootballSportsService;
use Illuminate\Support\Facades\DB;

class ProcessPlayerDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Number of records to process per chunk
    public $chunkSize;
    protected $footballApi;
    public function __construct($chunkSize = 50)
    {
        $this->chunkSize = $chunkSize;
        $this->footballApi = new FootballSportsService();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Get unfinished seasons
        $seasonIDs = FootballSeason::where(['finished' => false])->pluck('season_id');

        $playerIds = DB::select("
            SELECT DISTINCT player_id
            FROM football_team_players
            WHERE season_id IN (".implode(',', $seasonIDs->toArray()).")
        ");

        // Convert to simple array of IDs
        $playerIds = collect($playerIds)->pluck('player_id')->toArray();

        // Process in chunks
        foreach (array_chunk($playerIds, $this->chunkSize) as $chunk) {
            $playerInfo = [];

            foreach ($chunk as $playerId) {
                $response = $this->footballApi->getPlayerInfo($playerId);

                if ($response['success']) {
                    $data = $response['data'];
                    $playerInfo[] = $this->formatPlayerData($data);
                }
            }

            if (!empty($playerInfo)) {
                $this->upsertPlayers($playerInfo);
            }
        }
    }

    /**
     * Format player data for database insertion
     */
    protected function formatPlayerData($data)
    {
        return [
            'player_id' => $data['id'],
            'sport_id' => $data['sport_id'],
            'country_id' => $data['country_id'] ?? 0,
            'nationality_id' => $data['nationality_id'] ?? 0,
            'city_id' => $data['city_id'] ?? '',
            'position_id' => $data['position_id'] ?? 0,
            'detailed_position_id' => $data['detailed_position_id'] ?? 0,
            'type_id' => $data['type_id'] ?? 0,
            'common_name' => $data['common_name'] ?? 0,
            'firstname' => $data['firstname'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'name' => $data['name'] ?? '',
            'display_name' => $data['display_name'] ?? '',
            'image_path' => $data['image_path'] ?? '',
            'height' => $data['height'] ?? 0,
            'weight' => $data['weight'] ?? 0,
            'date_of_birth' => $data['date_of_birth'] ?? '1000-01-01',
            'gender' => $data['gender'] ?? '',
        ];
    }

    /**
     * Upsert players data in a transaction
     */
    protected function upsertPlayers($playerInfo)
    {
        DB::transaction(function () use ($playerInfo) {
            FootballPlayers::upsert(
                $playerInfo,
                ['player_id', 'sport_id'],
                [
                    'country_id',
                    'nationality_id',
                    'city_id',
                    'position_id',
                    'detailed_position_id',
                    'type_id',
                    'common_name',
                    'firstname',
                    'lastname',
                    'name',
                    'display_name',
                    'image_path',
                    'height',
                    'weight',
                    'date_of_birth',
                    'gender',
                ]
            );
        });
    }
}
