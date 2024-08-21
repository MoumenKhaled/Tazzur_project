<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdvisorsTable extends Migration {

	public function up()
	{
		Schema::create('advisors', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->string('name');
			$table->string('email');
			$table->string('password');
			$table->string('topics');
            $table->double('rating')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('advisors');
	}
}
