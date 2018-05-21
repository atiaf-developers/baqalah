@extends('layouts.backend')

@section('pageTitle', _lang('app.users'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/clients')}}">{{_lang('app.clients')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>

@endsection
@section('js')
{{--  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>  --}}
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/users.js" type="text/javascript"></script>
@endsection
@section('content')
{{--  <input type="hidden" name="lat" id="lat" value="{{ $data->lat}}">
<input type="hidden" name="lng" id="lng" value="{{ $data->lng }}">  --}}

<div class="row">
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-12">

                <!-- BEGIN SAMPLE TABLE PORTLET-->
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>{{ _lang('app.basic_info')}}
                        </div>
                        <!--                        <div class="tools">
                                                    <a href="javascript:;" class="collapse" data-original-title="" title="">
                                                    </a>
                        
                                                    <a href="javascript:;" class="remove" data-original-title="" title="">
                                                    </a>
                                                </div>-->
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-hover">

                                <tbody>
                                    <tr>
                                        <td>{{ _lang('app.first_name')}}</td>
                                        <td>{{$data->fname}}</td>

                                    </tr>
                                    <tr>
                                            <td>{{ _lang('app.last_name')}}</td>
                                            <td>{{$data->lname}}</td>
    
                                        </tr>
                                    <tr>
                                            <td>{{ _lang('app.username')}}</td>
                                            <td>{{$data->username}}</td>
    
                                        </tr>
                                    <tr>
                                        <td>{{ _lang('app.mobile')}}</td>
                                        <td>{{$data->mobile}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.email')}}</td>
                                        <td>{{$data->email}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.image')}}</td>
                                        <td><img style="width: 100px;height: 100px;" alt="" src="{{url('public/uploads/users')}}/{{$data->image}}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                 
                    </div>
                </div>
                <!-- END SAMPLE TABLE PORTLET-->


            </div>
        </div>
      
    </div>
    <div class="row">










    </div>


</div>


<script>
var new_lang = {

};

</script>
@endsection
