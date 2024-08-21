<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobTable extends Migration {

	public function up()
	{
		Schema::create('job', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->boolean('hidden_name');
			$table->string('job_title');
			$table->string('number_employees');
			$table->string('topic');
			$table->string('job_environment');
			$table->string('salary_fields');
			$table->string('education_level');
			$table->string('require_qualifications');
			$table->string('special_qualifications');
			$table->boolean('is_required_image');
			$table->string('required_languages');
			$table->string('experiense_years');
			$table->string('gender');
			$table->string('location');
			$table->biginteger('company_id')->unsigned();
			$table->boolean('is_required_license');
			$table->string('status');   //accepted  rejected  finite  
			$table->string('is_required_military');
			$table->string('job_time');
		//	$table->string('views');
			$table->date('end_date');
            $table->boolean('is_converted')->default(false);
		});
	}

	public function down()
	{
		Schema::drop('job');
	}
}
