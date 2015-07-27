<?php

class ApplicationController extends BaseController {

    public static $name = 'application';
    public static $group = 'application';
    /****************************************************************************/
    public static function returnRoutes($prefix = null) {

    }
    /****************************************************************************/
	public function __construct(){}
    /****************************************************************************/
    public static function returnInfo() {

        return array(
            'name' => self::$name,
            'group' => self::$group,
            'title' => 'Доктор на работе',
            'visible' => 1,
        );
    }

    public static function returnMenu() {

        $menu[] = array(
            'title' => 'Вопросы',
            'link' => QuestionsController::$name,
            'class' => 'fa-book',
            'system' => 1,
            'menu_child' => NULL,
            'permit' => 'view'
        );
        return $menu;
    }

    public static function returnActions() {

        return array(
            'view' => 'Просмотр',
            'create' => 'Создание',
            'edit' => 'Редактирование',
            'delete' => 'Удаление',
        );
    }
    /****************************************************************************/
}