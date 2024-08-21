<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionsTable extends Migration {

	public function up()
	{
		Schema::create('permissions', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('nam');
			$table->bigInteger('role_id')->unsigned();
			$table->string('permission_id');
		});
	}

	public function down()
	{
		Schema::drop('permissions');
	}
}
