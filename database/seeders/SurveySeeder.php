<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Survey;
use Faker\Factory as Faker;
class SurveySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 10; $i++) {
            Survey::create([
                'company_id' =>  $faker->numberBetween(1, 5),
                'title' => $faker->word,
                'description' => $faker->word,
            ]);
        }

    }
}
