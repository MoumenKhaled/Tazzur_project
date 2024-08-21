<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class CompaniesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $status = ['waiting', 'acceptable', 'rejected', 'banned'];

        $topics = [
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


        $types = [
            'Training', 'Hiring'
        ];

        for ($i = 1; $i <= 10; $i++) {

            $randomTypes = $faker->randomElements($types, mt_rand(1, 2));

            Company::create([
                'status' => $status[array_rand($status)],
                'name' => "Company Name {$i}",
                'phone' => "123456789{$i}",
                'topic' => $faker->randomElement($topics),
                'location_map' => json_encode(['lat' => 123.123, 'lng' => 456.456]),
                'location' => $faker->randomElement($locations),
                'fax' => "Fax{$i}",
                'documents' => json_encode([
                    "seeder/file2.txt",
                    "seeder/file.txt"
                ]),
                'type' => json_encode($randomTypes),
                'logo' => "seeder/profile.jpg",
                'otp_code' => str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => "company{$i}@example.com",
                'is_complete' => true,
                'email_verification' => $faker->sha1,
                'is_verified' => true,
                'password' => bcrypt('12345678'),
                'about_us' => "About Company {$i}",
                'email_verified_at' => Carbon::now(),
            ]);
        }
        $userIds = DB::table('users')->orderBy('id')->take(5)->pluck('id');


        $companyIds = DB::table('companies')->orderBy('id')->take(5)->pluck('id');


        foreach ($userIds as $userId) {
            foreach ($companyIds as $companyId) {
                DB::table('followers')->insert([
                    'user_id' => $userId,
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
