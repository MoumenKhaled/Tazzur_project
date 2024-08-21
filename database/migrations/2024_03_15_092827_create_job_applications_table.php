<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobApplicationsTable extends Migration {

	public function up()
	{
		Schema::create('job_applications', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->biginteger('user_id')->unsigned();
			$table->biginteger('job_id')->unsigned();
			$table->string('status')->default('pending'); //pending - accepted - rejected
            $table->integer('priority_application')->default(0);
		});
	}

	public function down()
	{
		Schema::drop('job_applications');
	}
}
