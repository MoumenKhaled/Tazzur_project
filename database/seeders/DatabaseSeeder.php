<?php

namespace Database\Seeders;
use Faker\Factory as Faker;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Manager;
use Database\Seeders\followersCompaniesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $managers = [
            [
                'name' => 'Test User',
                'email' => 'manager@example.com',
                'password' => bcrypt('12345678'),
                'role_name' => json_encode(['admin']),
            ],
            [
                'name' => 'Manager job_requests',
                'email' => 'manager1@example.com',
                'password' => bcrypt('12345678'),
                'role_name' => json_encode(['job_requests_coordinator']),
            ],
            [
                'name' => 'Manager job_posting_requests',
                'email' => 'manager2@example.com',
                'password' => bcrypt('12345678'),
                'role_name' => json_encode(['job_posting_requests_coordinator']),
            ],
            [
                'name' => 'Manager Three',
                'email' => 'manager3@example.com',
                'password' => bcrypt('12345678'),
                'role_name' => json_encode(['user_consultation_coordinator']),
            ],
            [
                'name' => 'Manager Four',
                'email' => 'manager4@example.com',
                'password' => bcrypt('12345678'),
                'role_name' => json_encode(['company_consultation_coordinator']),
            ],
            [
                'name' => 'Manager consultations',
                'email' => 'manager5@example.com',
                'password' => bcrypt('12345678'),
                'role_name' => json_encode(['user_consultation_coordinator', 'company_consultation_coordinator']),
            ],
            [
                'name' => 'Manager job requests',
                'email' => 'manager6@example.com',
                'password' => bcrypt('12345678'),
                'role_name' => json_encode(['job_posting_requests_coordinator', 'job_requests_coordinator']),
            ],
        ];

        foreach ($managers as $manager) {
            Manager::create($manager);
        }
/*
        $this->call([
            //UsersTableSeeder::class,
            UserCvSeeder::class,
            CompaniesTableSeeder::class,
            JobsTableSeeder::class,
            CoursesTableSeeder::class,
            CourseApplicationsTableSeeder::class,
            JobApplicationsTableSeeder::class,
            AdvisorTableSeeder::class,
            FormTableSeeder::class,
            FormQuestionTableSeeder::class,
           // FormOptionTableSeeder::class,
            SurveySeeder::class,
            SurveyOptionSeeder::class,
            UserConsultationSeeder::class,
            CompanyConsultationSeeder::class,
          //  followersCompaniesSeeder::class,

        ]);
        */
    }
}
