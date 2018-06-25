@extends('layouts.backend')

@section('pageTitle', _lang('app.app.Store'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/stores')}}">{{_lang('app.stores')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{ $store->name }}</span></li>

@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/stores.js" type="text/javascript"></script>
@endsection
@section('content')
<input type="hidden" name="lat" id="lat" value="{{ $store->lat}}">
<input type="hidden" name="lng" id="lng" value="{{ $store->lng }}">



<div class="row">
    <div class="col-md-6">
        <div class="col-md-12">

            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>{{ $store->name }}
                    </div>
                        <!--                        <div class="tools">
                                                    <a href="javascript:;" class="collapse" store-original-title="" title="">
                                                    </a>
                        
                                                    <a href="javascript:;" class="remove" store-original-title="" title="">
                                                    </a>
                                                </div>-->
                                            </div>
                                            <div class="portlet-body">
                                                <div class="table-scrollable">
                                                    <table class="table table-hover">

                                                        <tbody>
                                                            <tr>
                                                                <td>{{ _lang('app.name')}}</td>
                                                                <td>{{$store->name}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.mobile')}}</td>
                                                                <td>{{$store->mobile}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.email')}}</td>
                                                                <td>{{$store->email}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.owner_name')}}</td>
                                                                <td>{{$store->username}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.address')}}</td>
                                                                <td>{{$store->address}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.available')}}</td>
                                                                @if($store->available == 1)
                                                                <td>{{ _lang('app.opened')}}</td>
                                                                @else
                                                                <td>{{ _lang('app.closed')}}</td>
                                                                @endif


                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.active')}}</td>
                                                                @if($store->active == 1)
                                                                <td>{{ _lang('app.active')}}</td>
                                                                @else
                                                                <td>{{ _lang('app.not_active')}}</td>
                                                                @endif


                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.image')}}</td>
                                                                <td>
                                                                   <a class="fancybox-button" product-rel="fancybox-button" title="390 x 220 - keenthemes.com" href="{{url('public/uploads/stores')}}/{{$store->image}}">
                                                                    <img style="width: 100px;height: 100px;" alt="" src="{{url('public/uploads/stores')}}/{{$store->image}}">
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ _lang('app.description')}}</td>
                                                            <td>{{$store->description}}</td>

                                                        </tr>





                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- END SAMPLE TABLE PORTLET-->


                                </div>
                                <div class="col-md-12">


                                    <!-- BEGIN SAMPLE TABLE PORTLET-->
                                    <div class="portlet box red">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i> {{ $store->name }} {{ _lang('app.categories')}}
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            @foreach($store_categories as $category)
                                            <label class="label label-primary">{{ $category }}</label>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- BEGIN SAMPLE TABLE PORTLET-->
                                <div class="portlet box red">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>{{ $store->name }} {{ _lang('app.map')}}
                                        </div>

                                    </div>
                                    <div class="portlet-body">

                                        <div class="maplarger">

                                            <div id="map" style="height: 300px; width:100%;"></div>
                                            <div id="infowindow-content">
                                                <span id="place-name"  class="title"></span><br>
                                                Place ID <span id="place-id"></span><br>
                                                <span id="place-address"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                 

                    <script>
                        var new_lang = {};
                    </script>
                    @endsection
