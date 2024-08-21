<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name')->nullable();  // 3 digits without numbers
			$table->string('last_name')->nullable();  // 3 digits without numbers
			$table->string('phone')->nullable();  //
			$table->string('governorate')->nullable();  // text from front
			$table->string('address')->nullable();  //text
			$table->string('gender')->nullable();
			$table->string('marital_status')->nullable(); // enum
			$table->Date('birthday')->nullable(); //date validation
			$table->string('nationality')->nullable();  // text from
            $table->string('experience_years')->nullable();
            $table->string('education')->nullable();
            $table->json('topic')->nullable(); // list
            $table->boolean('driving_license')->nullable();   //true false
            $table->string('military_status')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('complete_state')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
