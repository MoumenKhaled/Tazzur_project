<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersConsultationTable extends Migration {

	public function up()
	{
		Schema::create('users_consultation', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->bigInteger('user_id')->unsigned();
			$table->bigInteger('advisor_id')->unsigned()->nullable();
			$table->text('user_message');
			$table->text('advisor_reply')->nullable();
			$table->string('topic');
            $table->double('rating')->nullable();
            $table->text('review')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('users_consultation');
	}
}
