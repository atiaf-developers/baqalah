@extends('layouts.backend')

@section('pageTitle', _lang('app.users'))

@section('js')

<script src="{{url('public/backend/js')}}/users.js" type="text/javascript"></script>
@endsection
@section('content')
<div class="modal fade" id="addEditUsers" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="addEditUsersLabel"></h4>
            </div>

            <div class="modal-body">


                <form role="form"  id="addEditUsersForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="0">
                    <input type="hidden" name="type" id="type" value="1">
                    <div class="form-body">
                        <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="name" name="fullname" placeholder="{{_lang('app.fullname')}}">
                            <label for="fullname">{{_lang('app.fullname')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="username" name="username" placeholder="{{_lang('app.username')}}">
                            <label for="username">{{_lang('app.username')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input col-md-6">
                            <input type="password" class="form-control" id="password" name="password" placeholder="{{_lang('app.password')}}">
                            <label for="password">{{_lang('app.password')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="email" name="email" placeholder="{{_lang('app.email')}}">
                            <label for="email">{{_lang('app.email')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="{{_lang('app.mobile')}}">
                            <label for="mobile">{{_lang('app.mobile')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class = "col-md-6 form-group form-md-line-input">
                            <select class = "form-control edited" id = "active" name = "active">
                                <option value = "1">{{_lang('app.active')}}</option>
                                <option value = "0">{{_lang('app.not_active')}}</option>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        {{-- <div class="form-group form-md-line-input col-md-9">
                            <textarea rows="5" class="form-control" name="about" id="about"></textarea>
                            <label for="about">{{_lang('app.about') }} (Optinal)</label>
                            <span class="help-block"></span>
                        </div> --}}
                        <div class="form-group col-md-3">
                            <label class="control-label">{{_lang('app.image')}}</label>
                            <div class="user_image_box image_uploaded_box">
                                <img src="{{url('no-image.png')}}" width="150" height="80" class="user_image" />
                            </div>
                            <input type="file" name="user_image" id="user_image" style="display:none;">
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

<div class="modal fade" id="sendMassage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">{{_lang("app.send")}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form role="form"  id="sendMassageForm"  enctype="multipart/form-data">
            <div class="modal-body">
                
                    {{ csrf_field() }}
                    <input type="hidden" name="user_id" id="user_id" value="0">
                    <input type="hidden" name="msg_id" id="msg_id">
                    <textarea name="massage" rows="5" class="form-control"></textarea>
                
            </div>
            
            <div class="modal-footer">
               <span class = "margin-right-10 loading hide"><i class = "fa fa-spin fa-spinner"></i></span>
              <button type="submit" class="btn btn-info submit-form">{{_lang("app.send")}}</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{_lang("app.close")}}</button>
            </div>
        </form>
        
          </div>
        </div>
      </div>

<div class = "panel panel-default">
    <div class = "panel-heading">
        <h3 class = "panel-title">{{_lang('app.users')}}</h3>
    </div>
    <div class = "panel-body">
        <!--Table Wrapper Start-->
        <!-- <div  style="padding: 30px; padding-top: 10px;width: 400px; margin: 40px auto 10px auto;position: relative;">
            <a class = "btn btn-info pull-left data-box active" data-type="1" style="height: 112px; padding-top: 50px;"> {{_lang('app.famous')}} </a>
            <a class = "btn btn-info pull-right data-box" data-type="2" style="height: 112px; padding-top: 50px;"> {{_lang('app.members')}} </a>
        </div> -->
        <div class="clearfix"></div>

        <div id="famous-table" class="table-container">
            <a class = "btn btn-sm btn-info pull-left" style = "margin-bottom: 40px;" href = "" onclick = "Users.add(); return false;" > {{_lang('app.add_new')}} </a>
            <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
                <thead>
                    <tr>
                        
                        <th>{{_lang('app.username')}}</th>
                        <th>{{ _lang('app.name') }}</th>
                        <th>{{_lang('app.image')}}</th>
                        <th>{{_lang('app.mobile')}}</th>
                        <th>{{_lang('app.status')}}</th>
                        <th>{{_lang('app.options')}}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    
        </div> 


        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
    var new_lang = {
        'add_user': "{{_lang('app.add_user')}}",
        'edit_user': "{{_lang('app.edit_user')}}",
        messages: {
            username: {
                required: lang.required

            },
            group_id: {
                required: lang.required

            },
            phone: {
                required: lang.required,
            },
            email: {
                required: lang.required,
                email: lang.email_not_valid,
            },
        }
    };
</script>
@endsection
