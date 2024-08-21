<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\CourseApplication;
class CourseApplicationsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $statuses = ['applied', 'rejected', 'interested'];
       
        for ($i = 1; $i <= 10; $i++)
        {
            CourseApplication::create([
                'user_id' => $i,
                'course_id' => $i,
                'status' => $faker->randomElement($statuses),
            ]);
        }
    }
}

