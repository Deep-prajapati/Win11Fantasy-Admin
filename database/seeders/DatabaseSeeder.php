<?php

namespace Database\Seeders;

use App\Models\ContestType;
use App\Models\DefaultContest;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        if(User::where('email', 'admin@gmail.com')->count() == 0) 
        {
            User::insert([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'mobile_number' => '0000000000',
                'role' => 1,
                'ref_code' => '1234qqwer',
                'invite_code' => '1234qqwer'
            ]);
        }

        if(DefaultContest::count() == 0) 
        {
            $this->call(DefaultContestPriceBreakupSeeder::class);
        }

        if(User::where('role', 3)->count() == 0) 
        {
            $this->call(BotUsersSeeder::class);
        }

        if(ContestType::count() == 0) 
        {
            $this->call(ContestTypeSeeder::class);
        }
    }
}
