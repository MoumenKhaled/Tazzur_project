<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormOption;
use App\Models\FormQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
class FormQuestionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = Form::all();
        $faker = Faker::create();
        $sampleQuestions = [
            'What is your preferred programming language?',
            'How many years of experience do you have?',
            'What is your highest level of education?',
            'Do you have experience with Laravel framework?',
            'Are you willing to relocate?'
        ];
        $sampleOptions = [
            'Option 1',
            'Option 2',
            'Option 3',
            'Option 4'
        ];

        foreach ($forms as $form) {
            for ($i = 0; $i < 5; $i++) {
                $question = FormQuestion::create([
                    'form_id' => $form->id,
                    'question' => $sampleQuestions[$i],
                ]);

                foreach ($sampleOptions as $option) {
                    FormOption::create([
                        'option_text' => $option,
                        'question_id' => $question->id,
                    ]);
                }
            }

    }
}
}
