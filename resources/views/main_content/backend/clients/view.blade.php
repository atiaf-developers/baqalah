@extends('layouts.backend')

@section('pageTitle', _lang('app.users'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/clients')}}">{{_lang('app.clients')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{ $user->fname.' '.$user->lname }}</span></li>

@endsection
@section('js')

<script src="{{url('public/backend/js')}}/users.js" type="text/javascript"></script>
@endsection
@section('content')


<div class="row">
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-12">

                <!-- BEGIN SAMPLE TABLE PORTLET-->
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>{{ _lang('app.user_info')}}
                        </div>
                        <!--                        <div class="tools">
                                                    <a href="javascript:;" class="collapse" user-original-title="" title="">
                                                    </a>
                        
                                                    <a href="javascript:;" class="remove" user-original-title="" title="">
                                                    </a>
                                                </div>-->
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-hover">

                                <tbody>
                                    <tr>
                                        <td>{{ _lang('app.first_name')}}</td>
                                        <td>{{$user->fname}}</td>

                                    </tr>
                                    <tr>
                                            <td>{{ _lang('app.last_name')}}</td>
                                            <td>{{$user->lname}}</td>
    
                                        </tr>
                                    <tr>
                                            <td>{{ _lang('app.username')}}</td>
                                            <td>{{$user->username}}</td>
    
                                        </tr>
                                    <tr>
                                        <td>{{ _lang('app.mobile')}}</td>
                                        <td>{{$user->mobile}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.email')}}</td>
                                        <td>{{$user->email}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.image')}}</td>
                                        <td><img style="width: 100px;height: 100px;" alt="" src="{{url('public/uploads/users')}}/{{$user->image}}"></td>
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
    


</div>


<script>
var new_lang = {

};

</script>
@endsection
