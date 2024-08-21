<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SurveyOption;
use Faker\Factory as Faker;
class SurveyOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            for($j = 1; $j<= 5; $j++){
                SurveyOption::create([
                    'survey_id' => $i,
                    'option_text' => $faker->sentence,
                    'vote_count' => $faker->numberBetween(0, 100),
                ]);
            }
        }
    }
}
