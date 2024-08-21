<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration {

	public function up()
	{
		Schema::create('companies', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->enum('status', array('waiting', 'acceptable', 'rejected', 'banned','Incomplete'));
			$table->string('name')->nullable();
			$table->string('phone')->nullable();
			$table->string('topic')->nullable();
            $table->json('location_map')->nullable();
			$table->string('location')->nullable();
			$table->string('fax')->default('000000');
			$table->json('documents')->nullable();
			$table->json('type')->nullable();
			$table->string('logo')->nullable();
			$table->string('otp_code')->nullable();
            $table->string('email')->unique()->nullable();
            $table->boolean('is_complete')->default(false);
            $table->string('email_verification')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
			$table->text('about_us')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('companies');
	}
}
