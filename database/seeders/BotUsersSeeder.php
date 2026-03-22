<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BotUsersSeeder extends Seeder
{
    public function run()
    {
        $botNames = [
            "Aarav", "Vivaan", "Aditya", "Vihaan", "Arjun", "Sai", "Reyansh", "Ayaan", "Krishna", "Ishaan",
            "Kabir", "Atharv", "Rudra", "Advait", "Om", "Shivansh", "Laksh", "Dhruv", "Kiaan", "Aryan",
            "Kushal", "Samarth", "Tanish", "Harsh", "Devansh", "Mihir", "Rohan", "Nirvaan", "Yug", "Tejas",
            "Siddharth", "Shreyas", "Ujjwal", "Yash", "Pranav", "Kunal", "Raj", "Harshit", "Saurabh", "Manav",
            "Gautam", "Arnav", "Aniket", "Nikhil", "Tanmay", "Ritik", "Himanshu", "Akhil", "Sandeep", "Suraj"
        ];

        $botUsers = [];

        foreach ($botNames as $name) 
        {
            $botUsers[] = [
                'name' => $name,
                'email' => Str::lower($name) . '@bot.com',
                'password' => bcrypt('password'), // Default password
                'country_code' => '+91',
                'mobile_number' => '0000000000',
                'role' => 3, // Bot user
                'is_banned' => 0,
                'is_kyc_complete' => 1,
                'otp_token' => null,
                'ref_code' => Str::random(10),
                'invite_code' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($botUsers);
    }
}
