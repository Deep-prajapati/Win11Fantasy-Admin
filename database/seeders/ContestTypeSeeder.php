<?php

namespace Database\Seeders;

use App\Models\ContestType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContestType::insert([
            [
                'id' => 28,
                'contest_type' => 'Mega',
                'description' => 'Fookri Mega',
                'max_entries' => 100,
                'free_wheel_count' => 0,
                'cancellable' => 'false',
                'is_deleted' => 0
            ],
            [
                'id' => 29,
                'contest_type' => 'Small',
                'description' => 'Fookri Small',
                'max_entries' => 20,
                'free_wheel_count' => 0,
                'cancellable' => 'false',
                'is_deleted' => 0
            ],
            [
                'id' => 30,
                'contest_type' => 'Mega',
                'description' => 'Mega Contests',
                'max_entries' => 1000000,
                'free_wheel_count' => 0,
                'cancellable' => 'true',
                'is_deleted' => 1
            ],
            [
                'id' => 31,
                'contest_type' => 'Head to head',
                'description' => 'header',
                'max_entries' => 2,
                'free_wheel_count' => 0,
                'cancellable' => 'true',
                'is_deleted' => 0
            ],
            [
                'id' => 32,
                'contest_type' => 'Roman Catholic Rohit',
                'description' => 'It will be only in July',
                'max_entries' => 500,
                'free_wheel_count' => 0,
                'cancellable' => 'true',
                'is_deleted' => 0
            ],
            [
                'id' => 33,
                'contest_type' => 'Very Mega',
                'description' => 'Very mega',
                'max_entries' => 10,
                'free_wheel_count' => 0,
                'cancellable' => 'true',
                'is_deleted' => 0
            ],
            [
                'id' => 34,
                'contest_type' => 'HUGE PRIZE',
                'description' => 'Guaranteed Contest',
                'max_entries' => 5555,
                'free_wheel_count' => 0,
                'cancellable' => 'false',
                'is_deleted' => 0
            ],
            [
                'id' => 35,
                'contest_type' => 'xyz',
                'description' => 'v gvh',
                'max_entries' => 5,
                'free_wheel_count' => 0,
                'cancellable' => 'false',
                'is_deleted' => 0
            ],
            [
                'id' => 36,
                'contest_type' => 'contest1',
                'description' => 'contest1',
                'max_entries' => 20,
                'free_wheel_count' => 0,
                'cancellable' => 'false',
                'is_deleted' => 0
            ],
            [
                'id' => 37,
                'contest_type' => 'SUPER GRAND LEAGUE',
                'description' => 'SUPER GRAND LEAGUE',
                'max_entries' => 10000,
                'free_wheel_count' => 0,
                'cancellable' => 'true',
                'is_deleted' => 0
            ],
            [
                'id' => 38,
                'contest_type' => 'Test',
                'description' => 'test',
                'max_entries' => 1,
                'free_wheel_count' => 0,
                'cancellable' => 'false',
                'is_deleted' => 0
            ],
        ]);
    }
}
