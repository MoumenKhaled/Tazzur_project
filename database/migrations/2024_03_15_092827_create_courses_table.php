<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoursesTable extends Migration {

	public function up()
	{
		Schema::create('courses', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->string('duration');
			$table->string('number_trainees');
			$table->string('topic');
			$table->string('type');
			$table->string('name');
			$table->biginteger('company_id')->unsigned();
			$table->date('start_date');
			$table->date('end_date');
			$table->integer('days');
			$table->string('price');
			$table->string('location');
            $table->string('status');
		});
	}

	public function down()
	{
		Schema::drop('courses');
	}
}
