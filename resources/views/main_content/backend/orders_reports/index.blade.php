@extends('layouts.backend')

@section('pageTitle', _lang('app.orders'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.orders')}}</span></li>
@endsection

@section('js')

<script src="{{url('public/backend/js')}}/orders_reports.js" type="text/javascript"></script>
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

                    <div class="form-group col-sm-6">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.users')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="user" id="user">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($users as $one)
                                <option {{ (isset($user) && $user==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->username}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-6">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.stores')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="store" id="store">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($stores as $one)
                                <option {{ (isset($store) && $store==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->name}}</option>
                                @endforeach


                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.delivery_type')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="delivery_type" id="delivery_type">
                                <option value="">{{_lang('app.choose')}}</option>
                                <option {{ (isset($delivery_type) && $delivery_type==1) ?'selected':''}}  value="1">{{ _lang('app.delivery_by_store') }}</option>
                                <option {{ (isset($delivery_type) && $delivery_type==2) ?'selected':''}}  value="2">{{ _lang('app.receiving_the_order') }}</option>


                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4 status-one-box" style="display:{{ (isset($delivery_type) && $delivery_type==1) ?'block':'none'}};">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.status')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="status_one" id="status_one">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($status_one_arr as $key=> $one)
                                <option {{ (isset($status_one) && $status_one==$key) ?'selected':''}} value="{{$key}}">{{$one['admin']}}</option>
                                @endforeach


                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4 status-two-box" style="display:{{ (isset($delivery_type) && $delivery_type==2) ?'block':'none'}};">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.status')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="status_two" id="status_two">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($status_two_arr as $key=> $one)
                                <option {{ (isset($status_two) && $status_two==$key) ?'selected':''}} value="{{$key}}">{{$one['admin']}}</option>
                                @endforeach


                            </select>
                        </div>
                    </div>
             
                    <div class="form-group col-md-4 col-md-offset-1">
                        <label class="col-sm-3 inputbox utbox control-label">{{ _lang('app.order_no') }}</label>
                        <div class="col-sm-9 inputbox">

                            <input type="text" class="form-control" placeholder=""  name="order" value="{{ (isset($order)) ? $order :'' }}">

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
        <div class="pull-left">
            <h3 class="panel-title">{{ _lang('app.search_results') }}</h3>
        </div>

        <div class="clearfix"></div>
    </div>
    <div class="panel-body">


        <div class="row">
            @if($orders->count()>0)
            <div class="col-sm-12">
                <table class = "table table-responsive table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{_lang('app.order_no')}}</th>
                            <th>{{_lang('app.client')}}</th>
                            <th>{{_lang('app.store')}}</th>
                            <th>{{_lang('app.total_price')}}</th>
                            <th>{{_lang('app.delivery_type')}}</th>
                            <th>{{_lang('app.status')}}</th>


                            <th colspan="2">{{_lang('app.created_at')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $one)
                        <tr>
                            <td>{{$one->id}}</td>
                            <td>{{$one->client_name}}</td>
                            <td>{{$one->store_name}}</td>
                            <td>{{$one->total_price}}</td>
                            <td>{{$one->delivery_type==1?_lang('app.delivery_by_store'):_lang('app.receiving_the_order')}}</td>
                            <td>
                                @if($one->delivery_type==1)
                                {{isset($status_one_arr[$one->status])?_lang('app.'.$status_one_arr[$one->status]['admin']):''}}
                                @else
                                {{isset($status_two_arr[$one->status])?_lang('app.'.$status_two_arr[$one->status]['admin']):''}}
                                @endif
                            </td>
                            <td>{{$one->created_at}}</td>
                            <td><a class="btn btn-sm btn-info" href="{{url('admin/orders_reports/'.$one->id)}}">{{_lang('app.details')}}</a></td>

                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.total_cost')}}</td>
                            <td colspan="4">{{$info->total_price}}</td>

                        </tr>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.total_commission_cost')}}</td>
                            <td colspan="4">{{$info->total_commission_cost}}</td>

                        </tr>




                    </tfoot>
                </table>
            </div>
            <div class="text-center">
                {{ $orders->links() }}  
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