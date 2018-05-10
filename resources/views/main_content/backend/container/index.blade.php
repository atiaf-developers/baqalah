@extends('layouts.backend')

@section('pageTitle', _lang('app.container'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.container')}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/container.js" type="text/javascript"></script>
@endsection
@section('content')
  {{ csrf_field() }}
<div class = "panel panel-default">
 
    <div class = "panel-body">
        <!--Table Wrapper Start-->
          <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn green" style="margin-bottom: 40px;" href="{{url('admin/container/create')}}">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>
    
       

        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{_lang('app.title')}}</th>
                    <th>{{_lang('app.delegate')}}</th>
                    <th>{{_lang('app.status')}}</th>
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

};
</script>
@endsection