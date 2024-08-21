<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Course;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $topics = [
            'Administration/Operations/Management',
            'Data Entry/Archiving',
            'Strategy/Consulting',
            'Research And Development/Statistics/Analyst',
            'IT/Software Development',
            'Banking/Insurance',
        ];
        $types = [
            'offline',
            'online'
        ];
        $locations = [
            'Damascus', 'Homs', 'Aleppo', 'Tartus', 'Latakia', 'Daraa'
        ];
        for ($i = 1; $i <= 10; $i++) {
            Course::create([
                'duration' => "{$i} hours",
                'number_trainees' => "{$i}0",
                'topic' => $faker->randomElement($topics),
                'type' => $faker->randomElement($types),
                'name' => "Course Name {$i}",
                'company_id' => $i,
                'start_date' => now(),
                'end_date' => now()->addDays($i),
                'days' => $i,
                'price' =>$faker->randomElement(['100000','20000']),
             //   'views' => $i * 10,
                'location' =>  $faker->randomElement($locations),
                'status'=>$faker->randomElement(['current', 'finite']),
            ]);
        }
    }
}
