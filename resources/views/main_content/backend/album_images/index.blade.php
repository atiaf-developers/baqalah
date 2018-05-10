@extends('layouts.backend')

@section('pageTitle', _lang('app.album_images'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('albums.index')}}">{{_lang('app.albums')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{ $album->title }}</span><i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.album_images')}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/album_images.js" type="text/javascript"></script>
@endsection
@section('content')
<div class="modal fade" id="addEditAlbumImages" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="addEditAlbumImagesLabel"></h4>
            </div>

            <div class="modal-body">


                <form role="form"  id="addEditAlbumImagesForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="0">
                    <input type="hidden" name="album_id" id="album_id" value="{{ $album->id }}">
                    <div class="form-body">

                        <div class="form-group form-md-line-input">
                            <input type="number" class="form-control" id="this_order" name="this_order" placeholder="{{_lang('app.this_order')}}">
                            <label for="this_order">{{_lang('app.this_order')}}</label>
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label">{{_lang('app.image')}}</label>
                            <div class="image_box">
                                <img src="{{url('no-image.png')}}" width="150" height="80" class="image" />
                            </div>
                            <input type="file" name="image" id="image" style="display:none;">
                            <div class="help-block"></div>
                        </div>

                        <div class="clearfix"></div>      


                    </div>


                </form>

            </div>

            <div class = "modal-footer">
                <span class = "margin-right-10 loading hide"><i class = "fa fa-spin fa-spinner"></i></span>
                <button type = "button" class = "btn btn-info submit-form"
                        >{{_lang("app.save")}}</button>
                <button type = "button" class = "btn btn-white"
                        data-dismiss = "modal">{{_lang("app.close")}}</button>
            </div>
        </div>
    </div>
</div>
<div class = "panel panel-default">

    <div class = "panel-body">
        <!--Table Wrapper Start-->
        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn green" style="margin-bottom: 40px;" href="" onclick="AlbumImages.add(); return false;">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>

        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{_lang('app.image')}}</th>
                    <th>{{_lang('app.this_order')}}</th>
                    <th>{{_lang('app.options')}}</th>
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
       album_id: {!! $album->id !!}
    };
</script>
@endsection