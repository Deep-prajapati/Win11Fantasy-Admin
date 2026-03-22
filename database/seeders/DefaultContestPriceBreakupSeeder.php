<?php

namespace Database\Seeders;

use App\Models\DefaultContest;
use App\Models\PrizeBreakup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultContestPriceBreakupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultContests = [
            [
                "id" => 1,
                "contest_type" => 1,
                "mrp" => 100000,
                "entry_fees" => 199,
                "total_spots" => 1000,
                "first_prize" => 25000,
                "prize_percentage" => 80,
                "winner_percentage" => 50,
                "cancellation" => "No",
                "total_winning_prize" => 80000,
                "is_free" => 0,
                "usable_bonus" => 10,
                "prize_breakup" => [
                    ["rank_from" => 1, "rank_upto" => 1, "prize_amount" => 25000],
                    ["rank_from" => 2, "rank_upto" => 5, "prize_amount" => 10000],
                    ["rank_from" => 6, "rank_upto" => 20, "prize_amount" => 5000],
                    ["rank_from" => 21, "rank_upto" => 50, "prize_amount" => 2000],
                ],
            ],
            [
                "id" => 2,
                "contest_type" => 2,
                "mrp" => 2000,
                "entry_fees" => 999,
                "total_spots" => 2,
                "first_prize" => 1800,
                "prize_percentage" => 90,
                "winner_percentage" => 50,
                "cancellation" => "No",
                "total_winning_prize" => 1800,
                "is_free" => 0,
                "usable_bonus" => 5,
                "prize_breakup" => [
                    ["rank_from" => 1, "rank_upto" => 1, "prize_amount" => 1800],
                ],
            ],
            [
                "id" => 3,
                "contest_type" => 3,
                "mrp" => 5000,
                "entry_fees" => 499,
                "total_spots" => 10,
                "first_prize" => 4500,
                "prize_percentage" => 90,
                "winner_percentage" => 10,
                "cancellation" => "Yes",
                "total_winning_prize" => 4500,
                "is_free" => 0,
                "usable_bonus" => 5,
                "prize_breakup" => [
                    ["rank_from" => 1, "rank_upto" => 1, "prize_amount" => 4500],
                ],
            ],
            [
                "id" => 4,
                "contest_type" => 4,
                "mrp" => 5000,
                "entry_fees" => 199,
                "total_spots" => 100,
                "first_prize" => 1000,
                "prize_percentage" => 80,
                "winner_percentage" => 50,
                "cancellation" => "No",
                "total_winning_prize" => 4000,
                "is_free" => 0,
                "usable_bonus" => 10,
                "prize_breakup" => [
                    ["rank_from" => 1, "rank_upto" => 10, "prize_amount" => 1000],
                    ["rank_from" => 11, "rank_upto" => 50, "prize_amount" => 500],
                ],
            ],
        ];
        
        foreach ($defaultContests as $key => $contest) 
        {
            DefaultContest::updateOrCreate(['id' => $contest['id'],'contest_type'=>$contest['contest_type']], [
                'mrp' => $contest['mrp'],
                'entry_fees'=> $contest['entry_fees'],
                'total_spots' => $contest['total_spots'],
                'first_prize' => $contest['first_prize'],
                'prize_percentage' => $contest['prize_percentage'],
                'winner_percentage' => $contest['winner_percentage'],
                'cancellation' => $contest['cancellation'],
                'total_winning_prize' => $contest['total_winning_prize'],
                'is_free' => $contest['is_free'] == 1,
                'usable_bonus' => $contest['usable_bonus'],
            ]);

            foreach ($contest['prize_breakup'] as $price) 
            {
                PrizeBreakup::updateOrCreate([
                    'default_contest_id' => $contest['id'],
                    'rank_from' => $price['rank_from'],
                    'rank_upto'=>$price['rank_upto'],
                ], [
                    'contest_type_id'=>$contest['contest_type'],
                    'prize_amount' => $price['prize_amount'],
                ]);
            }
        }
    }
}