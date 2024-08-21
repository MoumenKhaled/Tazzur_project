<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoleMangersTable extends Migration {

	public function up()
	{
		Schema::create('role_mangers', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->bigInteger('role_id')->unsigned();
			$table->bigInteger('manager_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('role_mangers');
	}
}
