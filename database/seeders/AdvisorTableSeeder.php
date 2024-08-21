<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use App\Models\Advisor;
class AdvisorTableSeeder extends Seeder
{
    public function run()
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

        for ($i = 1; $i <= 10; $i++) {
            Advisor::create([
                'name' => "Advisor Name {$i}",
                'email' => "advisor{$i}@example.com",
                'password' => bcrypt('12345678'),
                'topics' => $faker->randomElement($topicsList),
                'rating' => $faker->numberBetween(1, 5),
            ]);
        }
    }

}
