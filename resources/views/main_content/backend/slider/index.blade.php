@extends('layouts.backend')

@section('pageTitle', 'Page Title')
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>

<li><span> {{_lang('app.slider')}}</span></li>



@endsection
@section('js')
<script src="{{url('public/backend/js')}}/slider.js" type="text/javascript"></script>
@endsection
@section('content')
<div class="modal fade" id="addEditSlider" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="addEditSliderLabel"></h4>
            </div>

            <div class="modal-body">


                <form role="form"  id="addEditSliderForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="form-body">

                        <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="url" name="url" placeholder="{{ _lang('app.url') }}">
                            <label for="url">{{_lang('app.url')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input col-md-6">
                            <input type="number" class="form-control" id="this_order" name="this_order" placeholder="{{ _lang('app.this_order') }}">
                            <label for="this_order">{{_lang('app.this_order')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input col-md-6">
                            <select class="form-control edited" id="active" name="active">
                                <option  value="1">{{ _lang('app.active') }}</option>
                                <option  value="0">{{ _lang('app.not_active') }}</option>

                            </select>
                            <label for="active">{{_lang('app.status')}}</label>

                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">{{_lang('app.image')}}</label>
                            <div class="image_box image_uploaded_box">
                                <img src="{{url('no-image.png')}}" width="150" height="80" class="image" />
                            </div>
                            <input type="file" name="image" id="image" style="display:none;">
                            <div class="help-block"></div>
                        </div>







                    </div>


                </form>

            </div>

            <div class="modal-footer">
                <span class="margin-right-10 loading hide"><i class="fa fa-spin fa-spinner"></i></span>
                <button type="button" class="btn btn-info submit-form"
                        >{{_lang("app.save")}}</button>
                <button type="button" class="btn btn-white"
                        data-dismiss="modal">{{ _lang("app.close") }}</button>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
   
    <div class="panel-body">
            <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn green" style="margin-bottom: 40px;" href="" onclick="Slider.add(); return false;">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>
        <!--Table Wrapper Start-->

        <table class="table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{ _lang('app.image')}}</th>
                    <th>{{ _lang('app.active')}}</th>
                    <th>{{ _lang('app.this_order')}}</th>
                    <th>{{ _lang('app.options')}}</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
    var new_lang = {

    };
    var new_config = {

    };

</script>
@endsection
