<?php

return array(

    'sections' => function() {

        $settings = [];

        if (TRUE)
            $settings['main'] = [
                'title' => 'Основные',
                #'description' => 'Здесь собраны основные настройки сайта',
                'options' => array(
                    [
                        'group_title' => 'Настройки отправки почты',
                        'style' => 'margin: 0 0 5px 0',
                    ],

                    'feedback_address' => array(
                        'title' => 'Адрес почты для сообщений обратной связи',
                        'type' => 'text',
                    ),
                    'feedback_from_email' => array(
                        'title' => 'Адрес почты, от имени которого будут отправляться сообщения',
                        'type' => 'text',
                    ),
                    'feedback_from_name' => array(
                        'title' => 'Имя пользователя, от которого будут отправляться сообщения',
                        'type' => 'text',
                    ),

                    ['group_title' => 'Кеширование'],

                    'db_remember_timeout' => array(
                        'title' => 'Кол-во минут, на которое кешировать ВСЕ запросы к БД (не рекомендуется)',
                        'type' => 'text',
                    ),

                    ['group_title' => 'Прочее'],

                    'tpl_footer_counters' => array(
                        'title' => 'Код невидимых счетчиков (Я.Метрика, Google Analytics и т.д.)',
                        'type' => 'textarea',
                    ),
                ),
            ];

        if (Allow::action('catalog', 'catalog_allow', true, false))
            $settings['catalog'] = [
                'title' => 'Магазин',
                'options' => array(
                    'allow_products_order' => array(
                        'no_label' => true,
                        'title' => 'Разрешить сортировку всех товаров (не рекомендуется)',
                        'type' => 'checkbox',
                        'label_class' => 'normal_checkbox',
                    ),
                    'disable_attributes_for_products' => array(
                        'no_label' => true,
                        'title' => 'Отключить функционал работы с атрибутами для товаров',
                        'type' => 'checkbox',
                        'label_class' => 'normal_checkbox',
                    ),
                    'disable_attributes_for_categories' => array(
                        'no_label' => true,
                        'title' => 'Отключить функционал работы с атрибутами для категорий',
                        'type' => 'checkbox',
                        'label_class' => 'normal_checkbox',
                    ),
                ),
            ];

        return $settings;
    },

);