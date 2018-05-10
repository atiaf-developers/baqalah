@extends('layouts.backend')

@section('pageTitle', _lang('app.app.donation_requests'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/donation_requests')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>

@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/donation_requests.js" type="text/javascript"></script>
@endsection
@section('content')
<input type="hidden" name="lat" id="lat" value="{{ $donation_request->lat}}">
<input type="hidden" name="lng" id="lng" value="{{ $donation_request->lng }}">

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
                                        <td>{{ _lang('app.order_no')}}</td>
                                        <td>{{$donation_request->id}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.name')}}</td>
                                        <td>{{$donation_request->name}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.mobile')}}</td>
                                        <td>{{$donation_request->mobile}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.donation_type')}}</td>
                                        <td>{{$donation_request->donation_title}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.appropriate_time')}}</td>
                                        <td>{{$donation_request->appropriate_time}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.delegate')}}</td>
                                        <td>{{$donation_request->username}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.status')}}</td>
                                        <td>{{isset($status_arr[$donation_request->status]['admin']['message_'.$lang_code])?$status_arr[$donation_request->status]['admin']['message_'.$lang_code]:''}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.description')}}</td>
                                        <td>{{$donation_request->description}}</td>

                                    </tr>





                                </tbody>
                            </table>
                        </div>
                        @if(count($donation_request->images)>0)

                        <h3>{{_lang('app.gallery')}}</h3>
                        <ul class="list-inline blog-images">
                            @foreach($donation_request->images as $one)
                            <li>
                                <a class="fancybox-button" data-rel="fancybox-button" title="390 x 220 - keenthemes.com" href="{{$one}}">
                                    <img style="width: 100px;height: 100px;" alt="" src="{{$one}}">
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @endif
                    </div>
                </div>
                <!-- END SAMPLE TABLE PORTLET-->


            </div>
            <div class="col-md-12">
                <!-- BEGIN SAMPLE TABLE PORTLET-->
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>{{ _lang('app.assign_order')}}
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="" title="">
                            </a>

                            <a href="javascript:;" class="remove" data-original-title="" title="">
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form role="form"  id="assignedForm"  enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="order_id" value="{{$donation_request->id}}">
                            <div class = "form-group form-md-line-input">
                                <select class = "form-control edited" id = "delegate" name = "delegate">
                                    <option selected value = "">{{_lang('app.choose')}}</option>
                                    @foreach($delegates as $one)
                                    <option {{$donation_request->user_id==$one->id?'selected':''}} value = "{{$one->id}}">{{$one->username}}</option>
                                    @endforeach

                                </select>
                                <label for="delegate">{{_lang('app.delegate')}}</label>
                                <div class="help-block"></div>

                            </div>

                            <button type = "button" class = "btn btn-info submit-form"
                                    >{{_lang("app.save")}}</button>

                        </form>
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
    </div>
    <div class="row">










    </div>


</div>
@if($donation_request->status==7)
<div id="invoice-content" style="display: none;"> 
    @include('main_content/reports/invoice')
</div>
@endif

<script>
var new_lang = {

};

</script>
@endsection
