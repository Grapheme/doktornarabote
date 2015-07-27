<?php

class UserTableSeeder extends Seeder{

	public function run(){
		
		User::create(array(
            'group_id'=>1,
			'name'=>'Разработчик',
			'surname'=>'',
			'email'=>'developer@doktornarabote.ru',
			'active'=>1,
			'password'=>Hash::make('grapheme1234'),
			'photo'=>'',
			'thumbnail'=>'',
			'temporary_code'=>'',
			'code_life'=>0,
			'remember_token' => '',
		));
		User::create(array(
            'group_id'=>2,
			'name'=>'Администратор',
			'surname'=>'',
			'email'=>'admin@doktornarabote.ru',
			'active'=>1,
			'password'=>Hash::make('grapheme1234'),
			'photo'=>'',
			'thumbnail'=>'',
			'temporary_code'=>'',
			'code_life'=>0,
		));
	}
}