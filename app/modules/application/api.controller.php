<?php


class ApiController extends BaseController {

    public static $name = 'api';
    public static $group = 'application';

    private $json_request = array('error' => 1, 'data' => '', 'message' => '');

    /****************************************************************************/
    public static function returnRoutes() {
        $class = __CLASS__;
        Route::group(array('prefix' => 'api'), function () use ($class) {
            Route::get('help', array('uses' => $class . '@help'));
            Route::get('help/{method}', array('uses' => $class . '@helpMethod'));

            Route::any('register', array('uses' => $class . '@register'));
            Route::any('questions', array('uses' => $class . '@getQuestions'));
        });
    }

    /****************************************************************************/
    public function __construct() {
    }

    /****************************************************************************/
    public static function returnInfo() {
    }

    public static function returnMenu() {
    }

    public static function returnActions() {
    }

    /****************************************************************************/
    public function help() {
        $echo = '<a href="/api/help/register">Метод Register. Добавление пользователя в систему тестирования докторов</a>' . "<br>\n";
        $echo .= '<a href="/api/help/questions">Метод Questions. Получение списка вопросов</a>' . "<br>\n";
        Helper::ta($echo);
    }

    public function helpMethod($method) {

        $echo = '';
        switch ($method):
            case 'register' :
                $echo = 'Подсказка по методу Register. Добавление пользователя в систему тестирования докторов.' . "<br>\n";
                $echo .= 'Добавление пользователей происходит отправкой данных методами GET или POST.' . "<br>\n";
                $echo .= 'Результат JSON-строка следующего вида:' . "<br>\n";
                $echo .= 'Ошибка - {"error": 1,"data":"_пусто_","message":"_пусто_|E-mail уже зарегистрирован|Неверный токен"}.' . "<br>\n";
                $echo .= 'Успех - {"error": 0,"data":"_пусто_","message":"Аккаунт зарегистрирован"}.' . "<br><br>\n\n";
                $echo .= 'Пример вызова API с GET параметрами:' . "<br>\n";
                $echo .= '/api/register?token=' . md5('user@doctornarabote.ru' . 'doctor_on_work_testing') . '&remote_id=1&email=user@doctornarabote.ru&name=Доктор' . "<br>\n";
                $echo .= 'Где:' . "<br>\n";
                $echo .= '1) token = md5(email.secret_string). secret_string = секретная строка. email = E-mail пользователя ' . "<br>\n";
                $echo .= '2) remote_id = ID пользователя сайта' . "<br>\n";
                $echo .= '3) email = E-mail пользователя' . "<br>\n";
                $echo .= '4) name - Имя пользователя';
                break;
            case 'questions':
                $echo = 'Подсказка по методу Questions. Получение списка вопросов.' . "<br>\n";
                $echo .= 'Получение списка вопросов происходит отправкой данных методами GET или POST.' . "<br>\n";
                $echo .= 'Результат JSON-строка следующего вида:' . "<br>\n";
                $echo .= 'Ошибка - {"error": 1,"data":"_пусто_","message":"_пусто_|Неверный токен"}.' . "<br>\n";
                $echo .= 'Успех - {"error": 0,"data":"Массив данных","message":"_пусто_"}.' . "<br>\n";
                $echo .= 'Где data = [question: string (Текст вопроса), is_true: integer 0|1 (Это правда), answer: string (Текст правильного ответа), is_branding : integer 0|1 (Брендированный)]' . "<br><br>\n\n";
                $echo .= 'Пример вызова API с GET параметрами:' . "<br>\n";
                $echo .= '/api/questions?token=' . md5('questions' . 'doctor_on_work_testing') . "<br>\n";
                $echo .= 'Где:' . "<br>\n";
                $echo .= 'token = md5("questions".secret_string). secret_string = секретная строка. "questions" = строка questions ' . "<br>\n";
                break;
        endswitch;
        Helper::ta($echo);
    }

    /****************************************************************************/
    public function register() {

        $validator = Validator::make(Input::all(), array('token' => 'required', 'remote_id' => 'required|numeric',
            'email' => 'required|email', 'name' => 'required', 'password' => ''));
        if ($validator->passes()):
            $post = Input::all();
            if ($post['token'] == md5($post['email'] . Config::get('doktornarabote.secret_string'))):
                if (User::where('email', $post['email'])->exists() === FALSE):
                    $user = new User;
                    $user->remote_id = $post['remote_id'];
                    $user->group_id = Group::where('name', 'doctors')->pluck('id');
                    $user->name = $post['name'];
                    $user->email = $post['email'];
                    $user->active = 0;
                    $user->password = Hash::make('TSHZVixc');
                    $user->save();
                    $this->json_request['error'] = 0;
                    $this->json_request['message'] = 'Аккаунт зарегистрирован.';
                else:
                    $this->json_request['message'] = 'E-mail уже зарегистрирован.';
                endif;
            else:
                $this->json_request['message'] = 'Неверный токен';
            endif;
        endif;
        return Response::json($this->json_request, 200);
    }

    public function getQuestions() {

        $validator = Validator::make(Input::all(), array('token' => 'required'));
        if ($validator->passes()):
            $post = Input::all();
            if ($post['token'] == md5('questions' . Config::get('doktornarabote.secret_string'))):
                $questions = array();
                foreach (Questions::orderBy('order')->get() as $question):
                    $questions[] = array(
                        'question' => trim($question->question),
                        'is_true' => $question->is_true,
                        'answer' => trim($question->answer),
                        'is_branding' => $question->is_branding
                    );
                endforeach;
                $this->json_request['data'] = json_encode($questions);
            else:
                $this->json_request['message'] = 'Неверный токен';
            endif;
        endif;
        Helper::tad(json_decode($this->json_request['data'], TRUE));
        return Response::json($this->json_request, 200);
    }
    /****************************************************************************/
}