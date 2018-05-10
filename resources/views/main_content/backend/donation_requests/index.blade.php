@extends('layouts.backend')

@section('pageTitle', _lang('app.donation_requests'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.donation_requests')}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/donation_requests.js" type="text/javascript"></script>
@endsection
@section('content')
<form method="" id="orders-reports">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ _lang('app.filter_by') }}</h3>
        </div>
        <div class="panel-body">

            <div class="row">


                <div class="row">
                    <div class="form-group col-md-4 col-md-offset-1">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.from') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="date" class="form-control" placeholder=""  name="from" value="{{ (isset($from)) ? $from :'' }}">

                        </div>
                    </div>
                    <div class="form-group col-md-4 col-md-offset-1">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.to') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="date" class="form-control" placeholder=""  name="to" value="{{ (isset($to)) ? $to :'' }}">

                        </div>
                    </div>


                </div>
                <div class="row">
          
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.delegates')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="delegate" id="delegate">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($delegates as $one)
                                <option {{ (isset($delegate) && $delegate==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->username}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.status')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="status" id="status">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($status_filter as $key=> $one)
                                <option {{ (isset($status) && $status==$one) ?'selected':''}} value="{{$one}}">{{_lang('app.'.$one)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
         
    
                    <div class="form-group col-md-4 col-md-offset-1">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.request_no') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="text" class="form-control" placeholder=""  name="request" value="{{ (isset($request)) ? $request :'' }}">

                        </div>
                    </div>


                </div>









            </div>
            <!--row-->
        </div>
        <div class="panel-footer text-center">
            <button class="btn btn-info submit-form btn-report" type="submit">{{ _lang('app.apply') }}</button>
        </div>
    </div>
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ _lang('app.search_results') }}</h3>
    </div>
    <div class="panel-body">

        <div class="row">
            @if($donation_requests->count()>0)
            <div class="col-sm-12">
                <table class = "table table-responsive table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{_lang('app.request_no')}}</th>
                            <th>{{_lang('app.delegate')}}</th>
                            <th>{{_lang('app.donation_type')}}</th>
                            <th>{{_lang('app.appropriate_time')}}</th>
                            <th>{{_lang('app.status')}}</th>
                    
                            
                            <th colspan="2">{{_lang('app.created_at')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donation_requests as $one)
                        <tr>
                            <td>{{$one->id}}</td>
                            <td>{{$one->username}}</td>
                            <td>{{$one->donation_title}}</td>
                            <td>{{$one->appropriate_time}}</td>
                            <td>{{isset($status_arr[$one->status]['admin']['message_'.$lang_code])?$status_arr[$one->status]['admin']['message_'.$lang_code]:''}}</td>
              
                            <td>{{$one->created_at}}</td>
                            <td><a class="btn btn-sm btn-info" href="{{url('admin/donation_requests/'.$one->id)}}">{{_lang('app.details')}}</a></td>

                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.completed')}}</td>
                            <td colspan="4">{{$info->completed}}</td>

                        </tr>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.not_completed')}}</td>
                            <td colspan="4">{{$info->not_completed}}</td>

                        </tr>
            
                   

                    </tfoot>
                </table>
            </div>
            <div class="text-center">
                {{ $donation_requests->links() }}  
            </div>
            @else
            <p class="text-center">{{_lang('app.no_results')}}</p>
            @endif


        </div>
        <!--row-->
    </div>

</div>

<script>
var new_lang = {

};
</script>
@endsection