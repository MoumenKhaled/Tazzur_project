<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveysTable extends Migration {

	public function up()
	{
		Schema::create('surveys', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->biginteger('company_id')->unsigned();
			$table->string('title');
			$table->string('description');
		});
	}

	public function down()
	{
		Schema::drop('surveys');
	}
}
