@extends('layouts.backend')

@section('pageTitle',_lang('app.settings'))


@section('js')
<script src="{{url('public/backend/js')}}/notifications.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="notificationsForm"  enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="panel panel-default" id="editSiteSettings">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.settings') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">


                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="title" name="title">
                    <label for="title">{{_lang('app.title') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <textarea rows="5" class="form-control" name="message" id="message"></textarea>
                    <label for="message">{{_lang('app.message') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class = "form-group form-md-line-input col-md-4">
                    <select class = "form-control edited" id = "type" name = "type">
                        <option value = "">{{_lang('app.choose')}}</option>
                        <option value = "1">{{_lang('app.client')}}</option>
                        <option value = "2">{{_lang('app.worker')}}</option>
                    </select>
                    <label for="type">{{_lang('app.type')}}</label>

                </div>
                <div class="clearfix"></div>




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