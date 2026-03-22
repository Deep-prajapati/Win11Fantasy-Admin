<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Helpers\Helper;
use App\Models\Contest;
use App\Models\Fixture;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use App\Models\JoinCrickContest;
use Illuminate\Support\Facades\DB;
use App\Services\SportsMonkService;

class CommonController extends Controller
{

    public function getSettings(Request $request)
    {

        return Helper::SuccessReturn([
            'paymentPlateform' => $this->getPaymentPlateform(),
        ], 'Data fatched.');
    }
    protected function getPaymentPlateform()
    {
        return [
            'name' => 'Fookri',
            'method' => 1,
            'upi_id' =>  SiteSettings::getValue('payment_upi_info','games.fookri@okaxis'),
        ];
    }


    public function test()
    {
        $fixtures = Fixture::where('is_live', true)->get();
        // $fixtures = Fixture::where('fixture_id', 64110)->get();
        foreach ($fixtures as $match) {
            $contests = Contest::where(['match_id' => $match->fixture_id, 'is_cancelled' => false])->pluck('id');
            foreach ($contests as $contest) {
                $pointsString = JoinCrickContest::where(['match_id' => $match->fixture_id, 'contest_id' => $contest])
                    ->orderBy('points', 'desc')
                    ->pluck('points');
                if (count($pointsString) > 0) {
                    $pointsString = $pointsString->implode(',');
                    $entries = DB::table('join_crick_contests')
                        ->select('id', 'user_id', 'created_team_id', 'points')
                        ->selectRaw("FIND_IN_SET(points, '$pointsString') as ranks")
                        ->where(['match_id' => $match->fixture_id, 'contest_id' => $contest])
                        ->orderBy('ranks', 'asc')
                        ->get();
                    print_r($entries);
                    foreach ($entries as $entry) {
                        JoinCrickContest::where('id', $entry->id)->update(['ranks' => $entry->ranks]);
                    }
                    if ($match->status == "Finished") {
                        $match->is_live = false;
                        $match->is_completed = true;
                        $match->update();
                    }
                    if ($match->status == "Aban.") {
                        $match->is_live = false;
                        $match->is_cancelled = true;
                        $match->is_completed = false;
                        $match->update();
                    }
                }
            }
        }
    }

    public function playerReupdate()
    {
        // $teamIds = DB::table('fixtures')
        // ->select('localteam_id as team_id')
        // ->where('season_id', 1689)
        // ->union(
        //     DB::table('fixtures')
        //         ->select('visitorteam_id as team_id')
        //         ->where('season_id', 1689)
        // )
        // ->distinct()
        // ->pluck('team_id');
        // return $teamIds;
        $playersData = [];
        $teamIds = DB::table('fixtures')
            ->select('season_id', 'localteam_id as team_id')
            ->where('season_id', '!=', 1689)
            ->unionAll(
                DB::table('fixtures')
                    ->select('season_id', 'visitorteam_id as team_id')
                    ->where('season_id', '!=', 1689)
            )
            ->distinct()
            ->get();

        foreach ($teamIds as $team) {
            $apiservice = new SportsMonkService();
            $teamDetails = $apiservice->getteamSquad($team->team_id, $team->season_id);
            if (!isset($teamDetails['data']['squad'])) {
                continue; // Skip if no squad data
            }
            if (isset($teamDetails['data']['error'])) {
                print_r($teamDetails['data']);
                break; // Skip if no squad data
            }
            foreach ($teamDetails['data']['squad'] as $data) {
                $playersData[] = [
                    'player_id' => $data['id'],
                    'team_id' => $team->team_id,
                    'season_id' => $team->season_id,
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
                ];
            }
        }
        if (!empty($playersData)) {
            // return $playersData;
            DB::transaction(function () use ($playersData) {
                Player::upsert(
                    $playersData,
                    ['player_id', 'team_id', 'season_id'], // Unique key constraints
                    ['fullname', 'image_path', 'battingstyle', 'bowlingstyle', 'position_id', 'position_name', 'updated_at'] // Columns to update if conflict occurs
                );
            });
        }
    }
}
