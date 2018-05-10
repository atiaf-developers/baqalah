@extends('layouts.backend')

@section('pageTitle',_lang('app.profile'))


@section('js')
<script src="{{url('public/backend/js')}}/profile.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditProfileForm"  enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.profile') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input">
                    <input type="text" class="form-control" id="username" name="username" value="{{$User->username}}">
                    <label for="username">{{_lang('app.username')}}</label>
                    <span class="help-block"></span>
                </div>
                 <div class="form-group form-md-line-input">
                    <input type="password" class="form-control" id="password" name="password">
                    <label for="password">{{_lang('app.password')}}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input">
                    <input type="text" class="form-control" id="email" name="email" value="{{$User->email}}">
                    <label for="email">{{_lang('app.email')}}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input">
                    <input type="text" class="form-control" id="phone" name="phone" value="{{$User->phone}}">
                    <label for="phone">{{_lang('app.phone')}}</label>
                    <span class="help-block"></span>
                </div>

                @if ($User->type == 2)
                    <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="available" name="available">
                        <option {{ $available == true ? 'selected' : '' }} value="1">{{ _lang('app.available') }}</option>
                        <option {{ $available == false ? 'selected' : '' }} value="0">{{ _lang('app.not_available') }}</option>
                    </select>
                    <span class="help-block"></span>
                </div> 
                @endif

       
            </div>




            <!--Table Wrapper Finish-->
        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>

    </div>







</form>
@endsection