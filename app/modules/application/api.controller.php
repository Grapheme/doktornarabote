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
            Route::any('finish', array('uses' => $class . '@setRightAnswers'));
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
        $echo .= '<a href="/api/help/finish">Метод Finish. Запись количества правильных ответов</a>' . "<br>\n";
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
                $echo .= '/api/register?token=secret_string&remote_id=1&email=user@doctornarabote.ru&name=Доктор' . "<br>\n";
                $echo .= 'Где:' . "<br>\n";
                $echo .= '1) token = secret_string = секретная строка.' . "<br>\n";
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
                $echo .= 'Где data = [question: string (Текст вопроса), doctor_type : integer (Номер доктора), is_true: integer 0|1 (Это правда), answer: string (Текст правильного ответа), is_branding : integer 0|1 (Брендированный)]' . "<br><br>\n\n";
                $echo .= 'Пример вызова API с GET параметрами:' . "<br>\n";
                $echo .= "/api/questions?token=secret_string&doctor=1<br>\n";
                $echo .= 'Где:' . "<br>\n";
                $echo .= "token = secret_string = секретная строка<br>\n";
                $echo .= 'doctor - номер доктора или его имя. Возможные значения: 1 или уролог, 2 или гинеколог.'."<br>\n";
                $echo .= 'Например:'."<br>\n";
                $echo .= "/api/questions?token=secret_string&doctor=2<br>\n";
                $echo .= "/api/questions?token=secret_string&doctor=уролог<br>\n";
                $echo .= 'Внимание. Если не передавать параметр doctor в ответе будет полный список вопросов'."\n";
                break;
            case 'finish':
                $echo = 'Подсказка по методу Finish. Запись количества правильных ответов.' . "<br>\n";
                $echo .= 'Обновление количества правильных ответов происходит отправкой данных методами GET или POST.' . "<br>\n";
                $echo .= 'Результат JSON-строка следующего вида:' . "<br>\n";
                $echo .= 'Ошибка - {"error": 1,"data":"_пусто_","message":"_пусто_|Неверный токен"}.' . "<br>\n";
                $echo .= 'Успех - {"error": 0,"message":"Сохранено"}.' . "<br>\n";
                $echo .= 'Пример вызова API с GET параметрами:' . "<br>\n";
                $echo .= "/api/finish?token=secret_string&remote_id=1&right_answers=1<br>\n";
                $echo .= 'Где:' . "<br>\n";
                $echo .= '1) token = secret_string = секретная строка.' . "<br>\n";
                $echo .= '2) remote_id = ID пользователя сайта' . "<br>\n";
                $echo .= '3) right_answers = Количество правильных ответов';
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
            if ($post['token'] == Config::get('doktornarabote.secret_string')):
                if (User::where('remote_id', $post['remote_id'])->exists() === FALSE):
                    $user = new User;
                    $user->remote_id = $post['remote_id'];
                    $user->group_id = Group::where('name', 'doctors')->pluck('id');
                    $user->name = @$post['name'];
                    $user->email = @$post['email'];
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
        return Response::make(Input::get('callback').'('.json_encode($this->json_request).')', 200);
    }

    public function setRightAnswers(){

        $validator = Validator::make(Input::all(), array('token' => 'required', 'remote_id'=>'required', 'right_answers'=>'required'));
        if ($validator->passes()):
            $post = Input::all();
            if ($post['token'] == Config::get('doktornarabote.secret_string')):
                if($user = User::where('remote_id', Input::get('remote_id'))->first()):
                    $user->right_answers = Input::get('right_answers');
                    $user->test_date = date('Y-m-d H:i:s');
                    $user->save();
                    $user->touch();
                    $this->json_request['message'] = 'Сохранено';
                    $this->json_request['error'] = 0;
                endif;
            else:
                $this->json_request['message'] = 'Неверный токен';
            endif;
        endif;
        return Response::make(Input::get('callback').'('.json_encode($this->json_request).')', 200);
    }

    public function getQuestions() {

        $validator = Validator::make(Input::all(), array('token' => 'required', 'doctor'=>''));
        if ($validator->passes()):
            $post = Input::all();
            if ($post['token'] == Config::get('doktornarabote.secret_string')):
                $questions = array();
                if(Input::has('doctor')):
                    $doctor_type = Input::get('doctor');
                    $doctors_types = Config::get('doktornarabote.doctor_types');
                    $doctor_type_id = 0;
                    foreach($doctors_types as $index => $name):
                        if(is_numeric($doctor_type)):
                            if($index == (int)$doctor_type):
                                $doctor_type_id = $index;
                                break;
                            endif;
                        elseif(is_string($doctor_type)):
                            if(mb_strtolower(trim($name)) == mb_strtolower(trim($doctor_type))):
                                $doctor_type_id = $index;
                                break;
                            endif;
                        endif;
                    endforeach;
                    $questions_list = Questions::orderBy('order')->where('doctor_type', $doctor_type_id)->get();
                else:
                    $questions_list = Questions::orderBy('order')->get();
                endif;
                foreach ($questions_list as $question):
                    $questions[] = array(
                        'question' => trim($question->question),
                        'doctor_type' => $question->doctor_type,
                        'is_true' => $question->is_true,
                        'answer' => trim($question->answer),
                        'is_branding' => $question->is_branding
                    );
                endforeach;
                $this->json_request['data'] = $questions;
                $this->json_request['error'] = 0;
            else:
                $this->json_request['message'] = 'Неверный токен';
            endif;
        endif;
        return Response::make(Input::get('callback').'('.json_encode($this->json_request).')', 200);
    }
    /****************************************************************************/
}