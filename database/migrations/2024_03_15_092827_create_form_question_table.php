<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFormQuestionTable extends Migration {

	public function up()
	{
		Schema::create('form_question', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->biginteger('form_id')->unsigned();
			$table->string('question');
		});
	}

	public function down()
	{
		Schema::drop('form_question');
	}
}
