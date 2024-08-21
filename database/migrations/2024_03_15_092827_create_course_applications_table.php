<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseApplicationsTable extends Migration {

	public function up()
	{
		Schema::create('course_applications', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->biginteger('user_id')->unsigned();
			$table->biginteger('course_id')->unsigned();
			$table->string('status')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('course_applications');
	}
}
