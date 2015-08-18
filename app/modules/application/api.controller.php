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
                $echo .= '/api/register?token=' . md5('_user@doctornarabote.ru_' . 'doctor_on_work_testing') . '&remote_id=1&email=user@doctornarabote.ru&name=Доктор' . "<br>\n";
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
                $echo .= 'Где data = [question: string (Текст вопроса), doctor_type : integer (Номер доктора), is_true: integer 0|1 (Это правда), answer: string (Текст правильного ответа), is_branding : integer 0|1 (Брендированный)]' . "<br><br>\n\n";
                $echo .= 'Пример вызова API с GET параметрами:' . "<br>\n";
                $echo .= '/api/questions?token=' . md5('_questions_' . 'doctor_on_work_testing') . "&doctor=1<br>\n";
                $echo .= 'Где:' . "<br>\n";
                $echo .= 'token = md5("questions".secret_string). secret_string = секретная строка. "questions" = строка questions ' . "<br>\n";
                $echo .= 'doctor - номер доктора или его имя. Возможные значения: 1 или уролог, 2 или гинеколог.'."<br>\n";
                $echo .= 'Например:'."<br>\n";
                $echo .= '/api/questions?token=' . md5('_questions_' . 'doctor_on_work_testing') . "&doctor=2<br>\n";
                $echo .= '/api/questions?token=' . md5('_questions_' . 'doctor_on_work_testing') . "&doctor=уролог<br>\n";
                $echo .= 'Внимание. Если не передавать параметр doctor в ответе будет полный список вопросов'."\n";
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

        $validator = Validator::make(Input::all(), array('token' => 'required', 'doctor'=>''));
        if ($validator->passes()):
            $post = Input::all();
            if ($post['token'] == md5('questions' . Config::get('doktornarabote.secret_string'))):
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
                $this->json_request['data'] = json_encode($questions);
            else:
                $this->json_request['message'] = 'Неверный токен';
            endif;
        endif;
        return Response::json($this->json_request, 200);
    }
    /****************************************************************************/
}