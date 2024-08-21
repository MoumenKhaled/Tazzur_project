<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('users_consultation', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('users_consultation', function(Blueprint $table) {
			$table->foreign('advisor_id')->references('id')->on('advisors')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('companies_consultation', function(Blueprint $table) {
			$table->foreign('advisor_id')->references('id')->on('advisors')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('companies_consultation', function(Blueprint $table) {
			$table->foreign('company_id')->references('id')->on('companies')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('job_applications', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('job_applications', function(Blueprint $table) {
			$table->foreign('job_id')->references('id')->on('job')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('job', function(Blueprint $table) {
			$table->foreign('company_id')->references('id')->on('companies')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('user_cv', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});

		Schema::table('courses', function(Blueprint $table) {
			$table->foreign('company_id')->references('id')->on('companies')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('course_applications', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('course_applications', function(Blueprint $table) {
			$table->foreign('course_id')->references('id')->on('courses')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('usercv_courses', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('experiences', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('user_references', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('user_links', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('user_questions', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('user_questions', function(Blueprint $table) {
			$table->foreign('question_id')->references('id')->on('form_question')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('user_questions', function(Blueprint $table) {
			$table->foreign('form_id')->references('id')->on('forms')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('forms', function(Blueprint $table) {
			$table->foreign('job_id')->references('id')->on('job')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('form_question', function(Blueprint $table) {
			$table->foreign('form_id')->references('id')->on('forms')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('followers', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('followers', function(Blueprint $table) {
			$table->foreign('company_id')->references('id')->on('companies')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('surveys', function(Blueprint $table) {
			$table->foreign('company_id')->references('id')->on('companies')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('surveys_options', function(Blueprint $table) {
			$table->foreign('survey_id')->references('id')->on('surveys')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('votes', function(Blueprint $table) {
			$table->foreign('option_id')->references('id')->on('surveys_options')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('votes', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('form_options', function(Blueprint $table) {
			$table->foreign('question_id')->references('id')->on('form_question')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('role_permissions', function(Blueprint $table) {
			$table->foreign('role_id')->references('id')->on('roles')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('role_permissions', function(Blueprint $table) {
			$table->foreign('permission_id')->references('id')->on('permissions')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('role_mangers', function(Blueprint $table) {
			$table->foreign('role_id')->references('id')->on('roles')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('role_mangers', function(Blueprint $table) {
			$table->foreign('manager_id')->references('id')->on('managers')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
	}

	public function down()
	{
		Schema::table('users_consultation', function(Blueprint $table) {
			$table->dropForeign('users_consultation_user_id_foreign');
		});
		Schema::table('users_consultation', function(Blueprint $table) {
			$table->dropForeign('users_consultation_advisor_id_foreign');
		});
		Schema::table('companies_consultation', function(Blueprint $table) {
			$table->dropForeign('companies_consultation_advisor_id_foreign');
		});
		Schema::table('companies_consultation', function(Blueprint $table) {
			$table->dropForeign('companies_consultation_company_id_foreign');
		});
		Schema::table('job_applications', function(Blueprint $table) {
			$table->dropForeign('job_applications_user_id_foreign');
		});
		Schema::table('job_applications', function(Blueprint $table) {
			$table->dropForeign('job_applications_job_id_foreign');
		});
		Schema::table('job', function(Blueprint $table) {
			$table->dropForeign('job_company_id_foreign');
		});
		Schema::table('user_cv', function(Blueprint $table) {
			$table->dropForeign('user_cv_user_id_foreign');
		});

		Schema::table('courses', function(Blueprint $table) {
			$table->dropForeign('courses_company_id_foreign');
		});
		Schema::table('course_applications', function(Blueprint $table) {
			$table->dropForeign('course_applications_user_id_foreign');
		});
		Schema::table('course_applications', function(Blueprint $table) {
			$table->dropForeign('course_applications_course_id_foreign');
		});
		Schema::table('usercv_courses', function(Blueprint $table) {
			$table->dropForeign('usercv_courses_user_id_foreign');
		});
		Schema::table('experiences', function(Blueprint $table) {
			$table->dropForeign('experiences_user_id_foreign');
		});
		Schema::table('user_references', function(Blueprint $table) {
			$table->dropForeign('user_references_user_id_foreign');
		});
		Schema::table('user_links', function(Blueprint $table) {
			$table->dropForeign('user_links_user_id_foreign');
		});
		Schema::table('user_questions', function(Blueprint $table) {
			$table->dropForeign('user_questions_user_id_foreign');
		});
		Schema::table('user_questions', function(Blueprint $table) {
			$table->dropForeign('user_questions_question_id_foreign');
		});
		Schema::table('user_questions', function(Blueprint $table) {
			$table->dropForeign('user_questions_form_id_foreign');
		});
		Schema::table('forms', function(Blueprint $table) {
			$table->dropForeign('forms_job_id_foreign');
		});
		Schema::table('form_question', function(Blueprint $table) {
			$table->dropForeign('form_question_form_id_foreign');
		});
		Schema::table('followers', function(Blueprint $table) {
			$table->dropForeign('followers_user_id_foreign');
		});
		Schema::table('followers', function(Blueprint $table) {
			$table->dropForeign('followers_company_id_foreign');
		});
		Schema::table('surveys', function(Blueprint $table) {
			$table->dropForeign('surveys_company_id_foreign');
		});
		Schema::table('surveys_options', function(Blueprint $table) {
			$table->dropForeign('surveys_options_survey_id_foreign');
		});
		Schema::table('votes', function(Blueprint $table) {
			$table->dropForeign('votes_option_id_foreign');
		});
		Schema::table('votes', function(Blueprint $table) {
			$table->dropForeign('votes_user_id_foreign');
		});
		Schema::table('form_options', function(Blueprint $table) {
			$table->dropForeign('form_options_question_id_foreign');
		});
		Schema::table('role_permissions', function(Blueprint $table) {
			$table->dropForeign('role_permissions_role_id_foreign');
		});
		Schema::table('role_permissions', function(Blueprint $table) {
			$table->dropForeign('role_permissions_permission_id_foreign');
		});
		Schema::table('role_mangers', function(Blueprint $table) {
			$table->dropForeign('role_mangers_role_id_foreign');
		});
		Schema::table('role_mangers', function(Blueprint $table) {
			$table->dropForeign('role_mangers_manager_id_foreign');
		});
	}
}
