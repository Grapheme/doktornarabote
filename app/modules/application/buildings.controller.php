<?php


class BuildingsController extends BaseController {

    public static $name = 'buildings';
    public static $group = 'application';

    /****************************************************************************/
    public static function returnRoutes() {
        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => 'admin'), function () use ($class) {
            Route::resource('buildings', $class,
                array(
                    'except' => array('show'),
                    'names' => array(
                        'index' => 'buildings.index',
                        'create' => 'buildings.create',
                        'store' => 'buildings.store',
                        'edit' => 'buildings.edit',
                        'update' => 'buildings.update',
                        'destroy' => 'buildings.destroy'
                    )
                )
            );
        });
    }

    /****************************************************************************/
    public function __construct() {

        $this->module = array(
            'tpl' => static::returnTpl('admin'),
        );
        View::share('module', $this->module);
    }

    /****************************************************************************/
    public static function returnInfo() {
    }

    public static function returnMenu() {
    }

    public static function returnActions() {
    }

    /****************************************************************************/
    public function index() {

        $buildings = Buildings::orderBy('number')->get();
        return View::make($this->module['tpl'] . 'buildings.index', compact('buildings'));
    }

    public function create() {

        return View::make($this->module['tpl'] . 'buildings.create');
    }

    public function store() {

        $validator = Validator::make(Input::all(), Buildings::$rules);
        if ($validator->passes()):

            $build = new Buildings();
            $build->title = Input::get('title');
            $build->description = Input::get('description');
            $build->number = Input::get('number');
            $build->land_area = Input::get('land_area');
            $build->area = Input::get('area');
            $build->material = Input::get('material');
            $build->communication = Input::get('communication');
            $build->price = Input::get('price');
            $build->coordinates = Input::get('coordinates');
            $build->photo_id = Input::get('photo_id');
            $build->sold = 0;
            $build->save();

            $build->gallery_id = ExtForm::process('gallery', array('module' => 'Готовый дом', 'unit_id' => $build->id,
                'gallery' => Input::get('gallery'), 'single' => TRUE));
            $build->save();

            $json_request['responseText'] = "Дом добавлен";
            $json_request['redirect'] = URL::route('buildings.index');
            $json_request['status'] = TRUE;
        else:
            $json_request['responseText'] = 'Неверно заполнены поля';
            $json_request['responseErrorText'] = implode($validator->messages()->all(), '<br />');
        endif;
        return Response::json($json_request, 200);
    }

    public function edit($build_id) {

        if ($build = Buildings::where('id', $build_id)->with('gallery')->first()):
            return View::make($this->module['tpl'] . 'buildings.edit', compact('build'));
        else:
            App::abort(404);
        endif;
    }

    public function update($badge_id) {

        $validator = Validator::make(Input::all(), Buildings::$rules);
        if ($validator->passes()):

            $build = Buildings::where('id', $badge_id)->first();
            $build->title = Input::get('title');
            $build->description = Input::get('description');
            $build->number = Input::get('number');
            $build->land_area = Input::get('land_area');
            $build->area = Input::get('area');
            $build->material = Input::get('material');
            $build->communication = Input::get('communication');
            $build->price = Input::get('price');
            $build->coordinates = Input::get('coordinates');
            $build->photo_id = Input::get('photo_id');
            $build->sold = Input::has('sold') ? 1 : 0;
            $build->save();

            $build->gallery_id = ExtForm::process('gallery', array('module' => 'Готовый дом', 'unit_id' => $build->id,
                'gallery' => Input::get('gallery'), 'single' => TRUE));
            $build->save();

            $json_request['responseText'] = "Дом сохранен";
            $json_request['redirect'] = URL::route('buildings.index');
            $json_request['status'] = TRUE;
        else:
            $json_request['responseText'] = 'Неверно заполнены поля';
            $json_request['responseErrorText'] = implode($validator->messages()->all(), '<br />');
        endif;
        return Response::json($json_request, 200);
    }

    public function destroy($badge_id) {

        $json_request = array('status' => FALSE, 'responseText' => '', 'redirect' => FALSE);
        if (Request::ajax()):
            if ($gallery = Buildings::where('id', $badge_id)->first()->gallery):
                $photos = $gallery->photos;
                foreach ($gallery->photos as $photo):
                    if (!empty($photo->name) && File::exists(Config::get('site.galleries_photo_dir') . '/' . $photo->name)):
                        File::delete(Config::get('site.galleries_photo_dir') . '/' . $photo->name);
                    endif;
                    if (!empty($photo->name) && File::exists(Config::get('site.galleries_thumb_dir') . '/' . $photo->name)):
                        File::delete(Config::get('site.galleries_thumb_dir') . '/' . $photo->name);
                    endif;
                    $photo->delete();
                endforeach;
                $gallery->delete();
            endif;
            if ($photo = Buildings::where('id', $badge_id)->first()->photo):
                if (!empty($photo->name) && File::exists(Config::get('site.galleries_photo_dir') . '/' . $photo->name)):
                    File::delete(Config::get('site.galleries_photo_dir') . '/' . $photo->name);
                endif;
                if (!empty($photo->name) && File::exists(Config::get('site.galleries_thumb_dir') . '/' . $photo->name)):
                    File::delete(Config::get('site.galleries_thumb_dir') . '/' . $photo->name);
                endif;
                $photo->delete();
            endif;
            Buildings::where('id', $badge_id)->delete();

            $json_request['responseText'] = "Дом удален.";
            $json_request['status'] = TRUE;
        else:
            return Redirect::back();
        endif;
        return Response::json($json_request, 200);
    }
    /****************************************************************************/
}