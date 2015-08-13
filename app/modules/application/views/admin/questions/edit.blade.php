@extends(Helper::acclayout())
@section('style')
@stop
@section('content')
    @include($module['tpl'].'.questions.menu')
    {{ Form::model($question, array('route'=>array('questions.update',$question->id),'class'=>'smart-form','id'=>'questions-form','role'=>'form','method'=>'put')) }}
    {{ Form::hidden('title') }}
    {{ Form::hidden('order') }}
    <div class="row">
        <section class="col col-6">
            <div class="well">
                <header>Редактирование вопроса:</header>
                <fieldset>
                    <section>
                        <label class="label">Тип вопроса</label>
                        <label class="select">
                            {{ Form::select('is_branding', array('Обычный', 'Брендированный')) }}
                        </label>
                    </section>
                    <section>
                        <label class="label">Врач</label>
                        <label class="select">
                            {{ Form::select('doctor_type', Config::get('doktornarabote.doctor_types')) }}
                        </label>
                    </section>
                    <section>
                        <label class="label">Вопрос</label>
                        <label class="input">
                            {{ Form::textarea('question', NULL ,array('class'=>'redactor')) }}
                        </label>
                    </section>
                    <section>
                        <label class="label">Утверждение верное?</label>
                        <label class="select">
                            {{ Form::select('is_true', array('Нет, ложное.', 'Да, верное.')) }}
                        </label>
                    </section>
                    <section>
                        <label class="label">Ответ</label>
                        <label class="input">
                            {{ Form::textarea('answer', NULL ,array('class'=>'redactor')) }}
                        </label>
                    </section>
                </fieldset>
                <footer>
                    <a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner"
                       href="{{URL::previous()}}">
                        <i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
                    </a>
                    <button autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
                        <i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Сохранить</span>
                    </button>
                </footer>
            </div>
        </section>
    </div>
    {{ Form::close() }}
@stop
@section('scripts')
    <script>
        var essence = 'questions';
        var essence_name = 'вопрос';
        var validation_rules = {
            title: {required: true, maxlength: 100},
            question: {required: true},
            answer: {required: true},
        };
        var validation_messages = {
            title: {required: "Укажите название"},
            question: {required: "Укажите вопрос"},
            answer: {required: "Укажите ответ"},
        };
    </script>

    {{ HTML::script('private/js/modules/standard.js') }}

    {{ HTML::script('private/js/vendor/redactor.min.js') }}
    {{ HTML::script('private/js/system/redactor-config.js') }}
    <script type="text/javascript">
        if (typeof pageSetUp === 'function') {
            pageSetUp();
        }
        if (typeof runFormValidation === 'function') {
            loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}", runFormValidation);
        } else {
            loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}");
        }
    </script>
@stop