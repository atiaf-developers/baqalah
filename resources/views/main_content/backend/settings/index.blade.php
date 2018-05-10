@extends('layouts.backend')

@section('pageTitle',_lang('app.settings'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.settings')}}</span></li>

@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>

<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/settings.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="editSettingsForm"  enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="panel panel-default" id="editSiteSettings">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="phone" name="setting[phone]" value="{{$settings['phone']->value}}">
                    <label for="phone">{{_lang('app.phone') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="email" name="setting[email]" value="{{$settings['email']->value}}">
                    <label for="email">{{_lang('app.email') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="setting[store][android]" name="setting[store][android]" value="{{ isset($settings['store']->android) ? $settings['store']->android :'' }}">
                    <label for="">{{_lang('app.android_url') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="setting[store][ios]" name="setting[store][ios]" value="{{ isset($settings['store']->ios) ? $settings['store']->ios :'' }}">
                    <label for="phone">{{_lang('app.ios_url') }}</label>
                    <span class="help-block"></span>
                </div>
                {{--  <div class="form-group col-md-2">
                    <label class="control-label">{{_lang('app.about_image')}}</label>

                    <div class="about_image_box">
                        <img src="{{url('public/uploads').'/'.$settings['about_image']->value}}" width="100" height="80" class="about_image" />
                    </div>
                    <input type="file" name="about_image" id="about_image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>  --}}

                <div class="clearfix"></div>




            </div>




            <!--Table Wrapper Finish-->
        </div>

    </div>
    {{--  <div class="panel panel-default">

        <div class="panel-body">

            <div class="form-body">  --}}

                @foreach ($languages as $key => $value)
                <div class="panel panel-default">

                        <div class="panel-body">
                
                            <div class="form-body">
                <div class="col-md-12">
                    {{--  <div class="form-group form-md-line-input col-md-12">
                        <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{isset($settings_translations[$key])?$settings_translations[$key]->title:''}}">
                        <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$value.'') }}</label>
                        <span class="help-block"></span>
                    </div>  --}}
                    <div class="form-group form-md-line-input col-md-3">
                        <textarea class="form-control" id="description[{{ $key }}]" name="description[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->description:''}}</textarea>
                        <label for="description">{{_lang('app.description') }} {{ _lang('app. '.$value.'') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input col-md-3">
                        <textarea class="form-control" id="about[{{ $key }}]" name="about[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->about:''}}</textarea>
                        <label for="about">{{_lang('app.about') }} {{ _lang('app. '.$value.'') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input col-md-3">
                        <textarea class="form-control" id="address[{{ $key }}]" name="address[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->address:''}}</textarea>
                        <label for="address">{{_lang('app.address') }} {{ _lang('app. '.$value.'') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group form-md-line-input col-md-3">
                        <textarea class="form-control" id="policy[{{ $key }}]" name="policy[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->policy:''}}</textarea>
                        <label for="policy">{{_lang('app.policy') }} {{ _lang('app. '.$value.'') }}</label>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>




            <!--Table Wrapper Finish-->
        </div>

    </div>
                @endforeach

           
    <div class="panel panel-default">

        <div class="panel-body">

            <input value="{{ $settings['lat']->value }}" type="hidden" id="lat" name="setting[lat]">
            <input value="{{ $settings['lng']->value }}" type="hidden" id="lng" name="setting[lng]">
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




            <!--Table Wrapper Finish-->
        </div>

    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.social_media')}}</h3>
        </div>
        <div class="panel-body">

            <div class="form-body">
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][facebook]" value="{{ isset($settings['social_media']->facebook) ? $settings['social_media']->facebook :'' }}">
                    <label for="social_media_facebook">{{_lang('app.facebook') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][twitter]" value="{{ isset($settings['social_media']->twitter) ? $settings['social_media']->twitter :'' }}">
                    <label for="social_media_twitter">{{_lang('app.twitter') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][instagram]" value="{{ isset($settings['social_media']->instagram) ? $settings['social_media']->instagram :'' }}">
                    <label for="social_media_instagram">{{_lang('app.instagram') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][google]" value="{{ isset($settings['social_media']->google) ?$settings['social_media']->google :'' }}">
                    <label for="social_media_google">{{_lang('app.google') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][youtube]" value="{{ isset($settings['social_media']->youtube) ? $settings['social_media']->youtube :'' }}">
                    <label for="social_media_youtube">{{_lang('app.youtube') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="clearfix"></div>
            </div>




            <!--Table Wrapper Finish-->
        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>

    </div>







</form>
@endsection