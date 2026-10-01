<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PositionSeeder::class,
            QuestionCategorySeeder::class,
            QuestionSeeder::class,
            TestPackageSeeder::class,
            SettingSeeder::class,
            UserAndCandidateSeeder::class,
        ]);
    }
}
