@extends('layouts.backend')

@section('pageTitle',_lang('app.container'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/commen/')}}">{{_lang('app.container')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.container')}}</span></li>

@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>

<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>

<script src="{{url('public/backend/js')}}/container.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditContainerForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default" id="addEditContainer">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                <div class="row">

                    @foreach ($languages as $key => $value)
                    <div class="col-md-4">
                        <div class="form-group form-md-line-input col-md-12">
                            <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{ $title[$key] }}">
                            <label for="question">{{_lang('app.title') }} {{ _lang('app. '.$value.'') }}</label>
                            <span class="help-block"></span>
                        </div>
                        {{--  <div class="form-group form-md-line-input col-md-12">
                            <textarea class="form-control" id="answer[{{ $key }}]" name="answer[{{ $key }}]"  cols="30" rows="10"></textarea>
                            <label for="answer">{{_lang('app.answer') }} {{ _lang('app. '.$value.'') }}</label>
                            <span class="help-block"></span>
                        </div>  --}}
                    </div>

                    @endforeach

                </div>

               


            </div>
            <!--Table Wrapper Finish-->
        </div>
        
    </div>
    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">
                    <div class="form-group form-md-line-input col-md-6">
                    <select class="form-control edited" id="delegate_id" name="delegate_id">
                       
                        <option  value="">{{ _lang('app.select_delegate') }}</option>
                        @foreach($delegate as $value)
                        @if($data->delegate_id)
                            @if($data->delegate_id == $value->id)
                             <option  value="{{ $value->id }}" selected>{{ $value->username }}</option>
                            @else
                            <option  value="{{ $value->id }}">{{ $value->username }}</option>
                            @endif
                        @else
                        <option  value="{{ $value->id }}">{{ $value->username }}</option>
                        @endif
                            
                        @endforeach
                        
                    </select>
                    <label for="status">{{_lang('app.delegate') }}</label>
                    <span class="help-block"></span>
                </div> 
                <div class="form-group form-md-line-input col-md-6">
                    <select class="form-control edited" id="active" name="active">
                        @if($data->active==1)
                        <option  value="1" selected>{{ _lang('app.active') }}</option>
                        <option  value="0">{{ _lang('app.not_active') }}</option>
                        @else
                        <option  value="1">{{ _lang('app.active') }}</option>
                        <option  value="0" selected>{{ _lang('app.not_active') }}</option>
                        @endif
                       
                    </select>
                    <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

            </div>
        </div>
        


    </div>
  
    <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"></h3>
            </div>
            <div class="panel-body">
                    <div class="form-body">
                    <input value="{{ $data->lat }}" type="hidden" id="lat" name="lat">
                    <input value="{{ $data->lng }}" type="hidden" id="lng" name="lng">
                        <span class="help-block utbox"></span>
                    <div class="maplarger">
                                <input id="pac-input" class="controls" type="text"
                                       placeholder="Enter a location">
                                <div id="map" style="height: 500px; width:100%;"></div>
                                <div id="infowindow-content">
                                    <span id="place-name"  class="title"></span><br>
                                    <span id="place-address"></span>
                                </div>
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

};

</script>
@endsection