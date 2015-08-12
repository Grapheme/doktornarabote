<?php


class QuestionsController extends BaseController {

    public static $name = 'questions';
    public static $group = 'application';

    /****************************************************************************/
    public static function returnRoutes() {
        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => 'admin'), function () use ($class) {
            Route::resource('questions', $class,
                array(
                    'except' => array('show'),
                    'names' => array(
                        'index' => 'questions.index',
                        'create' => 'questions.create',
                        'store' => 'questions.store',
                        'edit' => 'questions.edit',
                        'update' => 'questions.update',
                        'destroy' => 'questions.destroy'
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

        $questions = Questions::orderBy('order')->get();
        return View::make($this->module['tpl'] . 'questions.index', compact('questions'));
    }

    public function create() {

        return View::make($this->module['tpl'] . 'questions.create');
    }

    public function store() {

        $validator = Validator::make(Input::all(), Questions::$rules);
        if ($validator->passes()):

            $question = new Questions();
            $question->order = Input::get('order');
            $question->title = substr(strip_tags(Input::get('question')), 45);
            $question->question = Input::get('question');
            $question->answer = Input::get('answer');
            $question->is_branding = Input::get('is_branding');
            $question->is_true = Input::get('is_true');
            $question->save();

            $json_request['responseText'] = "Вопрос добавлен";
            $json_request['redirect'] = URL::route('questions.index');
            $json_request['status'] = TRUE;
        else:
            $json_request['responseText'] = 'Неверно заполнены поля';
            $json_request['responseErrorText'] = implode($validator->messages()->all(), '<br />');
        endif;
        return Response::json($json_request, 200);
    }

    public function edit($question_id) {

        if ($question = Questions::where('id', $question_id)->first()):
            return View::make($this->module['tpl'] . 'questions.edit', compact('question'));
        else:
            App::abort(404);
        endif;
    }

    public function update($question_id) {

        $validator = Validator::make(Input::all(), Questions::$rules);
        if ($validator->passes()):

            $question = Questions::where('id', $question_id)->first();
            $question->order = Input::get('order');
            $question->title = substr(strip_tags(Input::get('title')), 45);
            $question->question = Input::get('question');
            $question->answer = Input::get('answer');
            $question->is_branding = Input::get('is_branding');
            $question->is_true = Input::get('is_true');
            $question->save();

            $json_request['responseText'] = "Вопрос сохранен";
            $json_request['redirect'] = URL::route('questions.index');
            $json_request['status'] = TRUE;
        else:
            $json_request['responseText'] = 'Неверно заполнены поля';
            $json_request['responseErrorText'] = implode($validator->messages()->all(), '<br />');
        endif;
        return Response::json($json_request, 200);
    }

    public function destroy($question_id) {

        $json_request = array('status' => FALSE, 'responseText' => '', 'redirect' => FALSE);
        if (Request::ajax()):
            Questions::where('id', $question_id)->delete();
            $json_request['responseText'] = "Вопрос удален.";
            $json_request['status'] = TRUE;
        else:
            return Redirect::back();
        endif;
        return Response::json($json_request, 200);
    }
    /****************************************************************************/
}