<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVotesTable extends Migration {

	public function up()
	{
		Schema::create('votes', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->bigInteger('survey_id')->unsigned();
			$table->bigInteger('option_id')->unsigned();
			$table->bigInteger('user_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('votes');
	}
}
