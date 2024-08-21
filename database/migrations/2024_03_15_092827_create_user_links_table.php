<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserLinksTable extends Migration {

	public function up()
	{
		Schema::create('user_links', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('title');
			$table->string('link');
			$table->biginteger('user_id')->unsigned();
		});
	}


	
	public function down()
	{
		Schema::drop('user_links');
	}
}
