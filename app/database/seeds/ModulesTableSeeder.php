<?php

class ModulesTableSeeder extends Seeder {

    public function run() {

        Module::create(array(
            'name' => 'system',
            'on' => 1,
            'order' => 999
        ));

    }

}