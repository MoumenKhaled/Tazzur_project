<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFormsTable extends Migration {

	public function up()
	{
		Schema::create('forms', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->boolean('is_required');
			$table->biginteger('job_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('forms');
	}
}
