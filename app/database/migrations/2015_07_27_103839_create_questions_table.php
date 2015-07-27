<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionsTable extends Migration {

	public function up(){
		Schema::create('questions', function(Blueprint $table){
			$table->increments('id');
			$table->integer('order')->unsigned()->nullable()->index();
			$table->string('title',128)->nullable();
			$table->boolean('is_branding')->default(0)->unsigned()->nullable();
			$table->boolean('is_true')->default(1)->unsigned()->nullable();
			$table->text('question')->nullable();
			$table->text('answer')->nullable();
			$table->timestamps();
		});
	}
	public function down(){
		Schema::drop('questions');
	}

}
