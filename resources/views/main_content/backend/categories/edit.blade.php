@extends('layouts.backend')

@section('pageTitle',_lang('app.edit_category'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
@if($path)
<li><a href="{{route('categories.index')}}">{{_lang('app.categories')}}</a> <i class="fa fa-circle"></i></li>
{!!$path!!}
<li><span> {{_lang('app.edit')}}</span></li>
@else
<li><span> {{_lang('app.categories')}}</span></li>
@endif
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/categories.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditCategoriesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.category_title') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $category->id }}">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{  $translations["$key"]->title }}">
                    <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach


            </div>
        </div>


    </div>

     @if ($parent_id != 0)
        <div class="panel panel-default" id="description">
            <div class="panel-heading">
                <h3 class="panel-title">{{_lang('app.category_description') }}</h3>
            </div>
            <div class="panel-body">

                <div class="form-body">
                   
                    @foreach ($languages as $key => $value)

                    <div class="form-group form-md-line-input col-md-6">
                        <textarea  class="form-control" id="description[{{ $key }}]" name="description[{{ $key }}]" cols="30" rows="10">{{  $translations["$key"]->description }}</textarea>
                        <label for="description">{{_lang('app.description') }} {{ _lang('app. '.$key.'') }}</label>
                        <span class="help-block"></span>
                    </div>

                    @endforeach


                </div>
            </div>
    </div>
     @endif
     


    <div class="panel panel-default">
       <div class="panel-heading">
                <h3 class="panel-title"></h3>
            </div>
        <div class="panel-body">

         
            <div class="form-body">
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $category->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="active" name="active">
                        <option {{ $category->active == 1 ?'selected' : '' }} value="1">{{ _lang('app.active') }}</option>
                        <option {{ $category->active == 0 ?'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                     <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

                <div class="clearfix"></div>
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="image_box">
                        @if ($category->image)
                            <img src="{{url('public/uploads/categories').'/'.$category->image}}" width="100" height="80" class="image" />
                        @else
                           <img src="{{url('no-image.png')}}" width="100" height="80" class="image" />
                        @endif
                       

                    </div>
                    <input type="file" name="image" id="image" style="display:none;">     
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