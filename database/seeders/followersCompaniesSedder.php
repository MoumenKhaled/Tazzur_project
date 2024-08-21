<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class followersCompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

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
