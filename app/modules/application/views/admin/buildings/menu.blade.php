<?
$menus = array();
$menus[] = array(
        'link' => URL::route('buildings.create'),
        'title' => 'Добавить',
        'class' => 'btn btn-primary'
);
?>
<h1 class="top-module-menu">
    <a href="{{ Request::path() }}">Готовые дома</a>
</h1>

{{ Helper::drawmenu($menus) }}
