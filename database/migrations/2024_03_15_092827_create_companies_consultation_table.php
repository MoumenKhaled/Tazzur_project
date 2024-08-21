<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesConsultationTable extends Migration {

	public function up()
	{
		Schema::create('companies_consultation', function(Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->softDeletes();
			$table->biginteger('advisor_id')->unsigned()->nullable();
			$table->biginteger('company_id')->unsigned();
			$table->text('user_message');
			$table->text('advisor_reply')->nullable();
			$table->string('topic');
            $table->double('rating')->nullable();
            $table->text('review')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('companies_consultation');
	}
}
