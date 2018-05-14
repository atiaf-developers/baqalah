@extends('layouts.backend')

@section('pageTitle',_lang('app.settings'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.settings')}}</span></li>

@endsection

@section('js')

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
                    <input type="number" class="form-control" id="search_range_for_stores" name="setting[search_range_for_stores]" value="{{ isset($settings['search_range_for_stores']) ? $settings['search_range_for_stores']->value : '' }}">
                    <label for="phone">{{_lang('app.search_range_for_stores') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="commission" name="setting[commission]" value="{{ isset($settings['commission']) ? $settings['commission']->value : '' }}" placeholder="%">
                    <label for="commission">{{_lang('app.commission') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="setting[stores_activation]" name="setting[stores_activation]">
                        <option {{ isset($settings['stores_activation']) && $settings['stores_activation']->value == 0 ? 'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
                        <option {{ isset($settings['stores_activation']) && $settings['stores_activation']->value == 1 ? 'selected' : '' }} value="1">{{ _lang('app.active') }}</option>

                    </select>
                    <label for="status">{{_lang('app.stores_activation_from_adminstration') }}</label>
                    <span class="help-block"></span>
                </div>

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


                                <div class="form-group form-md-line-input col-md-3">
                                    <textarea class="form-control" id="about_us[{{ $key }}]" name="about_us[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->about_us:''}}</textarea>
                                    <label for="about_us">{{_lang('app.about_us') }} {{ _lang('app.'.$value.'') }}</label>
                                    <span class="help-block"></span>
                                </div>

                                <div class="form-group form-md-line-input col-md-3">
                                    <textarea class="form-control" id="usage_conditions[{{ $key }}]" name="usage_conditions[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->usage_conditions:''}}</textarea>
                                    <label for="usage_conditions">{{_lang('app.usage_conditions') }} {{ _lang('app.'.$value.'') }}</label>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>




                        <!--Table Wrapper Finish-->
                    </div>

                </div>
                @endforeach



                <div class="panel panel-default">
                    <div class="panel-footer text-center">
                        <button type="button" class="btn btn-info submit-form"
                        >{{_lang('app.save') }}</button>
                    </div>

                </div>







            </form>
            @endsection