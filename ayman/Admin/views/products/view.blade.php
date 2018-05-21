@extends('layouts.backend')

@section('pageTitle', _lang('app.product'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/stores')}}">{{_lang('app.stores')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/products')}}/{{$data->store_id}}">{{_lang('app.products')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>

@endsection
@section('js')
{{--  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>  --}}
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/product.js" type="text/javascript"></script>
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
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-hover">

                                <tbody>
                                    <tr>
                                        <td>{{ _lang('app.Title')}}</td>
                                        <td>{{$data->name}}</td>

                                    </tr>
                                    <tr>
                                            <td>{{ _lang('app.quantity')}}</td>
                                            <td>{{$data->quantity}}</td>
    
                                        </tr>
                                    <tr>
                                            <td>{{ _lang('app.price')}}</td>
                                            <td>{{$data->price}}</td>
    
                                        </tr>
                                    <tr>
                                        <td>{{ _lang('app.category_name')}}</td>
                                        <td>{{$data->cat_name}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.description')}}</td>
                                        <td>{{$data->description}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{--  dd($data->images);  --}}
                        @if(count(json_decode($data->images))>0)
                       
                        <h3>{{_lang('app.gallery')}}</h3>
                        <ul class="list-inline blog-images">
                            @foreach(json_decode($data->images) as $one)
                            <li>
                                <a class="fancybox-button" data-rel="fancybox-button" title="390 x 220 - keenthemes.com" href="{{url('public/uploads/products')}}/{{$one}}">
                                    <img style="width: 100px;height: 100px;" alt="" src="{{url('public/uploads/products')}}/{{$one}}">
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @endif
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
