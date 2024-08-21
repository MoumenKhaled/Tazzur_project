<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFormOptionsTable extends Migration {

	public function up()
	{
		Schema::create('form_options', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('option_text');
			$table->bigInteger('question_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('form_options');
	}
}
