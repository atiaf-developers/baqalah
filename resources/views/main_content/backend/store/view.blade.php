@extends('layouts.backend')

@section('pageTitle', _lang('app.app.Store'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/stores')}}">{{_lang('app.stores')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>

@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/stores.js" type="text/javascript"></script>
@endsection
@section('content')
<input type="hidden" name="lat" id="lat" value="{{ $data->lat}}">
<input type="hidden" name="lng" id="lng" value="{{ $data->lng }}">


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
                                        <td>{{ _lang('app.name')}}</td>
                                        <td>{{$data->name}}</td>

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
                                        <td>{{ _lang('app.owner_name')}}</td>
                                        <td>{{$data->username}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.address')}}</td>
                                        <td>{{$data->address}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.avaliable')}}</td>
                                        @if($data->avaliable==0)
                                        <td>{{ _lang('app.avaliable')}}</td>
                                        @else
                                        <td>{{ _lang('app.not_avaliable')}}</td>
                                        @endif
                                        

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.image')}}</td>
                                        <td><img style="width: 100px;height: 100px;" alt="" src="{{url('public/uploads/stores')}}/{{$data->image}}"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.description')}}</td>
                                        <td>{{$data->description}}</td>

                                    </tr>





                                </tbody>
                            </table>
                        </div>
                 
                    </div>
                </div>
                <!-- END SAMPLE TABLE PORTLET-->


            </div>
        </div>
        <div class="col-md-6">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>{{ _lang('app.map')}}
                    </div>
                    <!--                    <div class="tools">
                                            <a href="javascript:;" class="collapse" data-original-title="" title="">
                                            </a>
                    
                                            <a href="javascript:;" class="remove" data-original-title="" title="">
                                            </a>
                                        </div>-->
                </div>
                <div class="portlet-body">

                    <div class="maplarger">
<!--                            <input id="pac-input" class="controls" type="text"
                               placeholder="Enter a location">-->
                        <div id="map" style="height: 300px; width:100%;"></div>
                        <!--                            <div id="infowindow-content">
                                                        <span id="place-name"  class="title"></span><br>
                                                        Place ID <span id="place-id"></span><br>
                                                        <span id="place-address"></span>
                                                    </div>-->
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
        <div class="col-md-6">
               

                        <!-- BEGIN SAMPLE TABLE PORTLET-->
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>{{ _lang('app.category')}}
                                </div>
                            </div>
                            <div class="portlet-body">
                                @foreach($categories as $category)
                                <label class="label label-primary">{{ $category->slug }}</label>
                                @endforeach
                            </div>
                        </div>
                
        </div>
    </div>
</div>



</div>


<script>
var new_lang = {

};

</script>
@endsection
