<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserReferencesTable extends Migration {

	public function up()
	{
		Schema::create('user_references', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('name');
			$table->string('employment');
			$table->string('email');
			$table->string('phone');
			$table->biginteger('user_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('user_references');
	}
}
