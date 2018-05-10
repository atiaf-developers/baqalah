@extends('layouts.backend')

@section('pageTitle',_lang('app.add_activities'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('activities.index')}}">{{_lang('app.activities')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.add_activities')}}</span></li>
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/activities.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditActivitiesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-6">

                    <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="">

                    <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                    <span class="help-block"></span>

                </div>

                @endforeach


            </div>
        </div>


    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-6">

                    <textarea class="form-control" id="description[{{ $key }}]" name="description[{{ $key }}]" value="" cols="30" rows="10"></textarea>

                    <label for="title">{{_lang('app.description') }} {{ _lang('app. '.$key.'') }}</label>
                    <span class="help-block"></span>

                </div>

                @endforeach


            </div>
        </div>


    </div>


    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">

                <div class="form-group form-md-line-input col-md-3">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="active" name="active">
                        <option  value="1">{{ _lang('app.active') }}</option>
                        <option  value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                     <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

            </div>
        </div>

    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.images') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <div class="form-group col-md-2">
                    <label class="control-label">1</label>

                    <div class="image_one_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_one" />
                    </div>
                    <input type="file" name="images[0]" id="image_one" style="display:none;">     
                    <span class="help-block"></span>             
                </div>
                <div class="form-group col-md-2">
                    <label class="control-label">2</label>

                    <div class="image_two_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_two" />
                    </div>
                    <input type="file" name="images[1]" id="image_two" style="display:none;">     
                    <span class="help-block"></span>             
                </div>
                <div class="form-group col-md-2">
                    <label class="control-label">3</label>

                    <div class="image_three_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_three" />
                    </div>
                    <input type="file" name="images[2]" id="image_three" style="display:none;">     
                    <span class="help-block"></span>             
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label">4</label>

                    <div class="image_four_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_four" />
                    </div>
                    <input type="file" name="images[3]" id="image_four" style="display:none;">     
                    <span class="help-block"></span>             
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label">5</label>

                    <div class="image_five_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_five" />
                    </div>
                    <input type="file" name="images[4]" id="image_five" style="display:none;">     
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
   
};

</script>
@endsection