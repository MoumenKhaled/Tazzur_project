<?php

namespace Database\Seeders;

use App\Models\FormOption;
use App\Models\FormQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
class FormOptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $questions = FormQuestion::all();
        foreach ($questions as $question) {
            FormOption::create([
                'option_text' => "Sample Option $question->id",
                'question_id' => $question->id,
            ]);
        }

    }
}
