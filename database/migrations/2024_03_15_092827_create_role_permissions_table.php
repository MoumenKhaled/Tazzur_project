<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolePermissionsTable extends Migration {

	public function up()
	{
		Schema::create('role_permissions', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->bigInteger('role_id')->unsigned();
			$table->bigInteger('permission_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('role_permissions');
	}
}
