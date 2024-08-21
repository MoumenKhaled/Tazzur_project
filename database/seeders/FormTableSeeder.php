<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Form;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
class FormTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::all();
        $faker = Faker::create();
        foreach ($jobs as $job) {
            Form::create([
                'is_required' => rand(0, 1),
                'job_id' => $job->id,
            ]);
        }
    }
}
