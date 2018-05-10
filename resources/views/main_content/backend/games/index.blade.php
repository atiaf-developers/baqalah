@extends('layouts.backend')

@section('pageTitle', _lang('app.games'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>

<li><span> {{_lang('app.games')}}</span></li>



@endsection

@section('js')
<script src="{{url('public/backend/js')}}/games.js" type="text/javascript"></script>
@endsection
@section('content')


<div class = "panel panel-default">
{{ csrf_field() }}
    <div class = "panel-body">


        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn green" style="margin-bottom: 40px;" href = "{{ route('games.create') }}" onclick="">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>

        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{_lang('app.title')}}</th>
                    <th>{{_lang('app.active')}}</th>
                    <th>{{_lang('app.category_order')}}</th>
                    <th>{{_lang('app.offers_order')}}</th>
                    <th>{{_lang('app.best_order')}}</th>
                    <th>{{_lang('app.price')}}</th>
                    <th>{{_lang('app.image')}}</th>
                    <th>{{_lang('app.category')}}</th>
                    <th>{{_lang('app.created_at')}}</th>
                    <th>{{_lang('app.options')}}</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
var new_lang = {

};
var new_config = {
   action:'index'
};
</script>
@endsection