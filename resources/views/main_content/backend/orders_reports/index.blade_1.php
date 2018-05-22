@extends('layouts.backend')

@section('pageTitle', _lang('app.reports'))

@section('js')

<script src="{{url('public/backend/scripts')}}/jspdf.min.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/orders_reports.js" type="text/javascript"></script>
@endsection
@section('content')
 <button onclick="Orders.generate()">Generate PDF</button>
<style>
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    text-align: left;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}
</style>
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
                    @if($User->type==1)
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.users')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="user" id="user">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($users as $one)
                                <option {{ (isset($user) && $user==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->first_name.' '.$one->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    @if($User->type==1)
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.resturantes')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="resturant" id="resturant">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($resturantes as $one)
                                <option {{ (isset($resturant) && $resturant==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                                @endforeach


                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="form-group col-sm-4">
                        <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.branches')}}</label>
                        <div class="col-sm-9 inputbox">
                            <select class="form-control" name="branch" id="branch">
                                <option value="">{{_lang('app.choose')}}</option>
                                @foreach($branches as $one)
                                <option {{ (isset($branch) && $branch==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
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
        <h3 class="panel-title">{{ _lang('app.search_results') }}</h3>
    </div>
    <div class="panel-body">

        <div class="row">
            @if($orders->count()>0)
            <div class="col-sm-12">
                <table class = "table table-responsive table-striped table-bordered table-hover" id="content">
                    <thead>
                        <tr>
                            <th>{{_lang('app.order_no')}}</th>
                            <th>{{_lang('app.client')}}</th>
                            <th>{{_lang('app.resturant')}}</th>
                            <th>{{_lang('app.total_cost')}}</th>
                         
                            <th>{{_lang('app.commission_cost')}}</th>
                          
                            <th>{{_lang('app.payment_method')}}</th>
                            <th colspan="2">{{_lang('app.date')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $one)
                        <tr>
                            <td>{{$one->id}}</td>
                            <td>{{$one->client_name}}</td>
                            <td>{{$one->resturant_title}}</td>
                            <td>{{$one->total_cost}}</td>
                        
                            <td>{{round($one->commission_cost,2)}}</td>
                         
                            <td>{{$one->payment_method}}</td>
                            <td>{{$one->date}}</td>
                            <td><a class="btn btn-sm btn-info" href="{{url('admin/orders_reports/'.$one->id)}}">{{_lang('app.details')}}</a></td>

                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center">

                            <td colspan="4">{{_lang('app.total_cost')}}</td>
                            <td colspan="4">{{$info->total_cost}}</td>

                        </tr>
                 
                        <tr class="text-center">
                            <td colspan="4">{{_lang('app.commission')}}</td>
                            <td colspan="4">{{round($info->commission_cost,2)}}</td>
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