<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Organization;
use App\Models\Prize;
use App\Models\PrizeUser;
use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\QuestWinner;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory(10)->create();
        Organization::factory(3)->create();
        Quest::factory(6)->create();
        Article::factory(10)->create();
        Prize::factory(6)->create();
        QuestParticipant::factory(10)->create();
        QuestWinner::factory(3)->create();
        PrizeUser::factory(5)->create();
    }
}
