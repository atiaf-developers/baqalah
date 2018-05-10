@extends('layouts.backend')

@section('pageTitle', $user->name )

@section('js')

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>

<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>

<script>
    Map.initMap(false, false, false, false);
</script>


@endsection
@section('content')
<input type="hidden" name="lat" id="lat" value="{{ $user->lat}}">
<input type="hidden" name="lng" id="lng" value="{{ $user->lng }}">

    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">

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
                                        <td>{{ _lang('app.name')}}</td>
                                        <td>{{$user->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.email')}}</td>
                                        <td>{{$user->email}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.username')}}</td>
                                        <td>{{$user->username}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.mobile')}}</td>
                                        <td>{{$user->mobile}}</td>

                                    </tr>

                                </tbody>
                            </table>
                        </div>




                        <ul class="list-inline blog-images">

                            <li>
                                <a class="fancybox-button" data-rel="fancybox-button" title="390 x 220 - keenthemes.com" href="{{ url("public/uploads/users/$user->image") }}">
                                    <img style="width: 100px;height: 100px;" alt="" src="{{ url("public/uploads/users/$user->image") }}">
                                </a>
                            </li>

                        </ul>


                    </div>
                </div>
                <!-- END SAMPLE TABLE PORTLET-->


            </div>

            <div class="col-md-6">
                <!-- BEGIN SAMPLE TABLE PORTLET-->
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>{{ _lang('app.location')}}
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
                            <div id="infowindow-content">
                                <span id="place-name"  class="title"></span><br>
                                Place ID <span id="place-id"></span><br>
                                <span id="place-address"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END SAMPLE TABLE PORTLET-->
            </div>
        </div>
    </div>


    <script>
var new_lang = {

};

    </script>

    @endsection
