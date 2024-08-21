<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExperiencesTable extends Migration {

	public function up()
	{
		Schema::create('experiences', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->biginteger('user_id')->unsigned();
			$table->string('company_name');
			$table->string('job_title');
			$table->date('start_date');
			$table->date('end_date');
			$table->text('details')->nullable();
			$table->string('name');
		});
	}

	public function down()
	{
		Schema::drop('experiences');
	}
}
