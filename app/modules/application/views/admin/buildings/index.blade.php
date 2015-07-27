@extends(Helper::acclayout())
@section('content')
    @include($module['tpl'].'buildings.menu')
    @if($buildings->count())
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <table class="table table-striped table-bordered min-table white-bg">
                    <thead>
                    <tr>
                        <th class="text-center" style="width:40px">#</th>
                        <th style="width:60%;" class="text-center">Название</th>
                        <th style="width:20%;" class="text-center">Цена</th>
                        <th class="width-250 text-center">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($buildings as $index => $build)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>Участок №{{ $build->number }}. {{ $build->title }}</td>
                            <td>{{ number_format($build->price, 2, '.', ' ') }} руб.</td>
                            <td class="text-center" style="white-space:nowrap;">
                                @if(Allow::action('application','edit'))
                                    <a class="btn btn-success margin-right-5"
                                       href="{{ URL::route('buildings.edit', $build->id) }}" title="Изменить">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                @endif
                                @if(Allow::action('application','delete'))
                                    {{ Form::open(array('route' => array('buildings.destroy', $build->id),'method'=>'delete','style'=>'display:inline-block')) }}
                                    <button type="button" class="btn btn-danger remove-build"><i
                                                class="fa fa-trash-o"></i></button>
                                    {{ Form::close() }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="ajax-notifications custom">
                    <div class="alert alert-transparent">
                        <h4>Список пуст</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
@section('scripts')
    <script>
        var essence = 'build';
        var essence_name = 'дом';
    </script>
    {{ HTML::script('private/js/modules/standard.js') }}
@stop
