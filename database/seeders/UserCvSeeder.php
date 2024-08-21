<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;

use App\Models\User_Cv;
use App\Models\Experience;
use App\Models\UserCourse;
use App\Models\UserReference;
use App\Models\UserLink;
class UserCvSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 20; $i++) {
            $user =  User::create([
                'email' => "user{$i}@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'first_name' => "First{$i}",
                'last_name' => "Last{$i}",
                'phone' => $faker->phoneNumber,
                'governorate' => $faker->city,
                'address' => $faker->address,


                'gender' => $faker->randomElement(['Male', 'Female']),
                'marital_status' => $faker->randomElement(['single', 'married']),
                'birthday' => $faker->date('Y-m-d', '-20 years'),
                'nationality' => $faker->country,
                'experience_years' => $faker->randomElement(['7 Years', '1 Year', '9 Years']),
                'education' => $faker->randomElement(['high school', 'bachelor degree', 'Doctorate', 'diploma']),
                'topic' => json_encode(
                    $faker->randomElements([
                        'Administration/Operations/Management',
                        'Data Entry/Archiving',
                        'Strategy/Consulting',
                        'Research And Development/Statistics/Analyst',
                        'IT/Software Development',
                        'Banking/Insurance',
                    ], 2)

                ),
                'driving_license' => $faker->boolean,
                'military_status' => $faker->randomElement(['finished', 'in service']),
                'complete_state' => $faker->boolean,
            ]);




        $userCv =User_Cv::create([
            'user_id' => $user->id,
            'work_city' => json_encode($faker->randomElement(['Damascus', 'Homs', 'Tartus', 'Quneitra'])),
            'job_current' => $faker->jobTitle,
            'image' => "seeder/profile.jpg",
            'languages' => json_encode($faker->randomElement(['English', 'Arabic'])),
            'cv_file' => "seeder/user.pdf",
            'job_level' => json_encode(['senior']),
            'job_environment' => json_encode($faker->randomElement(['offline','online'])),
            'job_time' => json_encode($faker->randomElement(['full time', 'part time'])),
            'job_field' => $faker->jobTitle,
            'skills' => $faker->word,
        ]);

        for ($j = 1; $j <= 3; $j++)
        {
            Experience::create([
                'user_id' => $user->id,
                'company_name' => $faker->company,
                'job_title' => $faker->jobTitle,
                'start_date' => $faker->date(),
                'end_date' => $faker->date(),
                'name' => $faker->name,
                'details' => $faker->paragraph,
            ]);
        }
            for ($k = 1; $k <= 2; $k++) {
                UserCourse::create([
                    'user_id' => $user->id,
                    'name' => $faker->word,
                    'source' => $faker->company,
                    'duration' => $faker->numberBetween(1, 12) . ' months',
                    'details' => $faker->paragraph,
                    'image' => "seeder/certification.png",
                ]);
            }


            for ($l = 1; $l <= 2; $l++) {
                UserReference::create([
                    'user_id' => $user->id,
                    'name' => $faker->name,
                    'employment' => $faker->jobTitle,
                    'email' => $faker->email,
                    'phone' => $faker->phoneNumber,
                ]);
            }

            for ($m = 1; $m <= 2; $m++) {
                UserLink::create([
                    'user_id' => $user->id,
                    'title' => $faker->word,
                    'link' => $faker->url,
                ]);
            }
        }
    }
}

