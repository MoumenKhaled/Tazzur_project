<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserQuestionsTable extends Migration {

	public function up()
	{
		Schema::create('user_questions', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->biginteger('user_id')->unsigned();
			$table->biginteger('question_id')->unsigned();
			$table->biginteger('form_options_id')->unsigned();
			$table->bigInteger('form_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('user_questions');
	}
}
