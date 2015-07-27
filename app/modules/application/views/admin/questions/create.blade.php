@extends(Helper::acclayout())
@section('style')
@stop
@section('content')
    @include($module['tpl'].'questions.menu')
    {{ Form::open(array('route'=>'questions.store','class'=>'smart-form','id'=>'questions-form','role'=>'form','method'=>'post')) }}
    {{ Form::hidden('order', (int) Questions::orderBy('order','DESC')->pluck('order') + 1) }}
    <div class="row">
        <section class="col col-6">
            <div class="well">
                <header>Добавление вопроса:</header>
                <fieldset>
                    <section>
                        <label class="label">Название</label>
                        <label class="input">
                            {{ Form::text('title') }}
                        </label>
                    </section>
                    <section>
                        <label class="label">Вопрос</label>
                        <label class="input">
                            {{ Form::textarea('question', NULL ,array('class'=>'redactor')) }}
                        </label>
                    </section>
                    <section>
                        <label class="checkbox">
                            {{ Form::checkbox('is_true', 1, TRUE) }}
                            <i></i>Это правда
                        </label>
                    </section>
                    <section>
                        <label class="label">Ответ</label>
                        <label class="input">
                            {{ Form::textarea('answer', NULL ,array('class'=>'redactor')) }}
                        </label>
                    </section>
                    <section>
                        <label class="checkbox">
                            {{ Form::checkbox('is_branding', 1, NULL) }}
                            <i></i>Брендированный
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