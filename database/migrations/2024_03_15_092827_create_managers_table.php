<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagersTable extends Migration {

	public function up()
	{
		Schema::create('managers', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->string('email');
			$table->string('password');
			$table->json('role_name');
			$table->string('name');
		});
	}

	public function down()
	{
		Schema::drop('managers');
	}
}
