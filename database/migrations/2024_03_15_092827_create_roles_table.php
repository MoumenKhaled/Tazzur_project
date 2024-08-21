<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesTable extends Migration {

	public function up()
	{
		Schema::create('roles', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->enum('name', array('coordinator', 'assistant'));
		});
	}

	public function down()
	{
		Schema::drop('roles');
	}
}
