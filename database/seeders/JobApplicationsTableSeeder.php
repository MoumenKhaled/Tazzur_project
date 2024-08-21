<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\FormQuestion;
use App\Models\JobApplication;

class JobApplicationsTableSeeder extends Seeder
{
    public function run()
    {

        $faker = Faker::create();
        for ($i = 1; $i <= 10; $i++) {
            JobApplication::create([
                'user_id' => $i,
                'job_id' => $i,
                'status' => 'pending',
                'priority_application' =>0,
            ]);
        }
    }
}
