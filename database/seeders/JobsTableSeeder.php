<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Job;
class JobsTableSeeder extends Seeder
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

        $locations = [
            'Homs', 'As-Suwayda', 'Damascus'
        ];


        for ($i = 1; $i <= 10; $i++) {

             Job::create([
                'hidden_name' => $faker->boolean,
                'job_title' => 'Software Developer',
                'number_employees' => rand(1, 50),
                'topic' => $faker->randomElement($topicsList),
                'job_environment' => $faker->randomElement(['offline','online']),
                'salary_fields' => '1000',
                'education_level' =>$faker->randomElement(['high school', 'bachelor degree', 'Doctorate', 'diploma']),
                'require_qualifications' => 'Strong programming skills',
                'special_qualifications' => 'Experience with Laravel framework',
                'is_required_image' => $faker->boolean,
                'required_languages' => json_encode('English'),
                'experiense_years' => $faker->randomElement(['7 Years', '1 Year', '9 Years']),
                'gender' => $faker->randomElement(['Male', 'Female']),
                'location' => $faker->randomElement($locations),
                'company_id' => rand(1, 10),
                'is_required_license' => $faker->boolean,
                'status' => $faker->randomElement(['current', 'finite']),
                'is_required_military' => $faker->randomElement(['finished', 'in service']),
                'job_time' =>$faker->randomElement(['full time', 'part time']),
                'end_date' => '2025-12-31',
            ]);
        }
    }
}
