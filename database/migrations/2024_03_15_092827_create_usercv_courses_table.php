<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsercvCoursesTable extends Migration {

	public function up()
	{
		Schema::create('usercv_courses', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('name');
			$table->string('source');
			$table->string('duration');
			$table->biginteger('user_id')->unsigned();
			$table->string('image')->nullable();
			$table->text('details')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('usercv_courses');
	}
}
