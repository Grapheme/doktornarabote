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
            'title' => 'Вяземские сады',
            'visible' => 1,
        );
    }

    public static function returnMenu() {

        $menu_child[] = array(
            'title' => 'Готовы дома',
            'link' => 'buildings',
            'class' => 'fa-circle',
        );
        $menu_child[] = array(
            'title' => 'Участки',
            'link' => 'land',
            'class' => 'fa-circle-o',
        );
        $menu[] = array(
            'title' => 'Каталог',
            'link' => '#',
            'class' => 'fa-book',
            'system' => 1,
            'menu_child' => $menu_child,
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