@extends('layouts.backend')

@section('pageTitle',_lang('app.edit_location'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
@if($path)
<li><a href="{{url('admin/locations')}}">{{_lang('app.locations')}}</a> <i class="fa fa-circle"></i></li>
{!!$path!!}
<li><span> {{_lang('app.edit')}}</span></li>
@else
<li><span> {{_lang('app.locations')}}</span></li>
@endif
@endsection


@section('js')
<script src="{{url('public/backend/js')}}/locations.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditLocationsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $location->id }}">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{ $location_translations["$key"] }}">
                    <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach
                <br>

            </div>
        </div>


    </div>


    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $location->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="prefix" name="prefix" value="{{ $location->prefix }}">
                    <label for="prefix">{{_lang('app.prefix') }}</label>
                    <span class="help-block"></span>
                </div>

            </div>
        </div>


    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.supervisor_info') }}</h3>
        </div>
        <div class="panel-body">

            <div class="form-body">
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="supervisor_image_box">
                        <img src="{{url('public/uploads/supervisors').'/'.$location->supervisor_image}}" width="100" height="80" class="supervisor_image" />
                    </div>
                    <input type="file" name="supervisor_image" id="supervisor_image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>



                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="supervisor_name" name="supervisor_name" value="{{ $location->name }}">
                    <label for="name">{{_lang('app.supervisor_name') }}</label>
                    <span class="help-block"></span>
                </div>


                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="supervisor_contact_numbers" name="supervisor_contact_numbers" value="{{ $location->contact_numbers }}" placeholder="+966663635,+96651515156,....">
                    <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
                    <span class="help-block"></span>
                </div>

            </div>
        </div>

        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>


    </div>


</form>
<script>
var new_lang = {

};
var new_config = {
    parent_id: "{{$parent_id}}"
};

</script>
@endsection