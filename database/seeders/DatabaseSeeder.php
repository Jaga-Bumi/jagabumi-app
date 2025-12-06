<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Organization;
use App\Models\Quest;
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
    }
}
