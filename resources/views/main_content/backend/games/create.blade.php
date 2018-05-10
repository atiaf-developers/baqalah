@extends('layouts.backend')

@section('pageTitle',_lang('app.add'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('games.index')}}">{{_lang('app.games')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.create')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/games.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditGamesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">

                <div class="col-md-9">
                    @foreach ($languages as $key => $value)
                    <div class="form-group form-md-line-input col-md-6">
                        <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="">
                        <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$value.'') }}</label>
                        <span class="help-block"></span>
                    </div>
                    @endforeach
                    @foreach ($languages as $key => $value)
                    <div class="form-group form-md-line-input col-md-6">
                        <textarea class="form-control" id="description[{{ $key }}]" name="description[{{ $key }}]"  cols="20" rows="30"></textarea>
                        <label for="description">{{_lang('app.description') }} {{ _lang('app. '.$value.'') }}</label>
                        <span class="help-block"></span>
                    </div>

                    @endforeach
                </div>
                <div class="col-md-3">
                    <div class="form-group form-md-line-input">
                        <input type="text" class="form-control" id="price" name="price" value="">
                        <label for="price">{{_lang('app.price') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input">
                        <input type="text" class="form-control" id="discount_price" name="discount_price" value="">
                        <label for="discount_price">{{_lang('app.discount_price') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input">
                        <input type="text" class="form-control" id="over_price" name="over_price" value="">
                        <label for="over_price">{{_lang('app.over_price') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input">
                        <input type="number" class="form-control" id="category_order" name="category_order" value="">
                        <label for="category_order">{{_lang('app.category_order') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input">
                        <input type="number" class="form-control" id="offers_order" name="offers_order" value="">
                        <label for="offers_order">{{_lang('app.offers_order') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input">
                        <input type="number" class="form-control" id="best_order" name="best_order" value="">
                        <label for="best_order">{{_lang('app.best_order') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input">
                        <select class="form-control edited" id="active" name="active">
                            <option  value="1">{{ _lang('app.active') }}</option>
                            <option  value="0">{{ _lang('app.not_active') }}</option>
                        </select>
                        <label for="status">{{_lang('app.status') }}</label>
                        <span class="help-block"></span>
                    </div>

                    <div class="form-group form-md-line-input">
                        <select class="form-control" id="category" name="category">

                            @foreach ($categories as $key => $value)
                            <option value = "{{$value->id}}">{{$value->title}}</option>
                            @endforeach
                        </select>
                        <label for = "category">{{_lang('app.category')}}</label>
                    </div>
                </div>


            </div>
        </div>


    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.youtube_url') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <div class="form-group form-md-line-input col-md-12">
                    <input type="hidden" name="youtube_url" id="youtube_url" value="">
                    <input type="text" class="form-control" id="youtube_video_url" value="">
                    <label for="youtube_video_url">{{_lang('app.youtube_url') }}</label>
                    <span class="help-block"></span>
                </div>
                <div id="youtube-iframe" class="col-md-12">

                </div>

            </div>
        </div>


    </div>





    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.gallery') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <div class="form-group col-md-2">
                    <label class="control-label">1</label>

                    <div class="image_one_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_one" />
                    </div>
                    <input type="file" name="gallery[0]" id="image_one" style="display:none;">     
                    <span class="help-block"></span>             
                </div>
                <div class="form-group col-md-2">
                    <label class="control-label">2</label>

                    <div class="image_two_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_two" />
                    </div>
                    <input type="file" name="gallery[1]" id="image_two" style="display:none;">     
                    <span class="help-block"></span>             
                </div>
                <div class="form-group col-md-2">
                    <label class="control-label">3</label>

                    <div class="image_three_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image_three" />
                    </div>
                    <input type="file" name="gallery[2]" id="image_three" style="display:none;">     
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
    action: 'add'
};

</script>
@endsection