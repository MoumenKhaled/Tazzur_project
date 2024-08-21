<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;
class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 10; $i++) {
            User::create([
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
                'experience_years' => $faker->randomElement(['10 Years', '1 Year', '5 Years']),
                'education' => $faker->randomElement(['high school', 'bachelor degree', 'Doctorate', 'diploma']),
                'topic' => json_encode([$faker->word, $faker->word]),
                'driving_license' => $faker->boolean,
                'military_status' => $faker->randomElement(['finished', 'in service']),
                'complete_state' => $faker->boolean,
            ]);
        }


      
    }
}

