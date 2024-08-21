<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserCvTable extends Migration {

	public function up()
	{
		Schema::create('user_cv', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->json('work_city');  //list
			$table->string('job_current');  // string
			$table->string('image')->nullable();
			$table->json('languages')->nullable();
			$table->string('cv_file')->nullable();
			$table->biginteger('user_id')->unsigned();
			$table->json('job_level')->nullable();  //not found
			$table->json('job_environment')->nullable();  // online - offline    //list
			$table->json('job_time')->nullable();  //part time - full time   //list
			$table->string('job_field')->nullable();
			$table->string('skills')->nullable(); //   "skills":"bb"
		});
	}

	public function down()
	{
		Schema::drop('user_cv');
	}
}
