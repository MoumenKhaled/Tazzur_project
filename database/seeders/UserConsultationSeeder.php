<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserConsultation;
use Faker\Factory as Faker;
class UserConsultationSeeder extends Seeder
{

    public function run(): void
    {
        $faker = Faker::create();
        $topicsList = [
            'Administration/Operations/Management',
            'Data Entry/Archiving',
            'Strategy/Consulting',
            'Research And Development/Statistics/Analyst',
            'IT/Software Development',
            'Banking/Insurance',
        ];
        for ($i = 0; $i < 10; $i++) {
            $advisorId = $i < 4 ? null : $faker->numberBetween(1, 10);
            $advisorReply = $i < 4 ? null : $faker->paragraph;

            $review = $i < 4 ? null : $faker->paragraph;
            $rating = $i < 4 ? null : $faker->numberBetween(1, 5);
            UserConsultation::create([
                'user_id' => $faker->numberBetween(1, 10),
                'advisor_id' => $advisorId,
                'user_message' => $faker->paragraph,
                'advisor_reply' => $advisorReply,
                'topic' =>$faker->randomElement($topicsList),
                'created_at' => now(),
                'updated_at' => now(),

                'review'=> $review,
                'rating' => $rating,
            ]);
        }
    }
}
