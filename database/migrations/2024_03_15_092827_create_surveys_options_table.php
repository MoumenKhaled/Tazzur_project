<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveysOptionsTable extends Migration {

	public function up()
	{
		Schema::create('surveys_options', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->biginteger('survey_id')->unsigned();
			$table->string('option_text');
			$table->bigInteger('vote_count');
		});
	}

	public function down()
	{
		Schema::drop('surveys_options');
	}
}
