@extends('layouts.backend')

@section('pageTitle')
{{_lang('app.orders') }}
@endsection
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/orders_reports')}}">{{_lang('app.orders')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>
@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/orders_reports.js" type="text/javascript"></script>
@endsection
@section('content')


<div class="row">
    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-shopping-cart"></i>Order  <span class="hidden-480">
                        | {{ $order->created_at }} </span>
                </div>
                <div class="actions">
                    <a href="{{url('admin/resturant_orders')}}" class="btn default yellow-stripe">
                        <i class="fa fa-angle-left"></i>
                        <span class="hidden-480">
                            {{  _lang('back') }} </span>
                    </a>
                    <a href="javascript:;" onclick="My.print('invoice-content')" class="btn default yellow-stripe">
                        <i class="fa fa-print" aria-hidden="true"></i>
                        <span class="hidden-480">
                            {{  _lang('print') }}</span>
                    </a>

                </div>
            </div>
            <div class="portlet-body">
                <div class="tabbable">
                    <ul class="nav nav-tabs nav-tabs-lg">
                        <li class="active">
                            <a href="#tab_1" data-toggle="tab" aria-expanded="true">
                                {{ _lang('app.details') }} </a>
                        </li>

                        <!--                        <li class="">
                                                    <a href="#tab_2" data-toggle="tab" aria-expanded="true">
                                                        {{ _lang('app.reply') }} </a>
                                                </li>-->



                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="portlet yellow-crusta box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>Order Details
                                            </div>
                                            <!--                                            <div class="actions">
                                                                                            <a href="javascript:;" class="btn btn-default btn-sm"><span class="md-click-circle md-click-animate" style="height: 67px; width: 67px; top: -17.5px; left: 18.1406px;"></span>
                                                                                                <i class="fa fa-pencil"></i> Edit </a>
                                                                                        </div>-->
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.username')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->client_name}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.order_no')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->id}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.store')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->store_name}}
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.payment_method')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->delivery_type==1?_lang('app.delivery_by_store'):_lang('app.receive_order')}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.status')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    @if($order->delivery_type==1)
                                                    {{isset($status_one[$order->status])?_lang('app.'.$status_one[$order->status]['admin']):''}}
                                                    @else
                                                    {{isset($status_two[$order->status])?_lang('app.'.$status_two[$order->status]['admin']):''}}
                                                    @endif
                                                    
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.total_price')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->total_price}}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 col-sm-12">
                                    <div class="portlet blue-hoki box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>{{_lang('app.purchase_info')}}
                                            </div>

                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.name')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->name}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.mobile')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->mobile}}
                                                </div>
                                            </div>
                                            @if($order->delivery_type==1)
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.building')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->building}}
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.floor')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->floor}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.location')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    <input type="hidden" name="lat" id="lat" value="{{ $order->lat}}">
                                                    <input type="hidden" name="lng" id="lng" value="{{ $order->lng }}">
                                                    <div id="map" style="height: 300px; width:100%;"></div>
                                                </div>
                                            </div>
                                            @endif


                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="portlet grey-cascade box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>Shopping Cart
                                            </div>

                                        </div>
                                        <div class="portlet-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th> {{_lang('app.no')}}</th>
                                                            <th> {{_lang('app.product')}}</th>
                                                            <th> {{_lang('app.price')}}</th>
                                                            <th> {{_lang('app.quantity')}}</th>
                                                            <th> {{_lang('app.total_price')}}</th>


                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($order->details as $key=> $one)
                                                        <tr>
                                                            <td> {{$key+1}} </td>
                                                            <td>
                                                                <h5>{{$one->name}}</h5>

                                                            </td>
                                                            <td>{{$one->price}}</td>
                                                            <td>{{$one->quantity}}</td>
                                                            <td>{{$one->total_price}}</td>
                                                        </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="well">




                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.total_price') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->total_price }} {{ $currency_sign }}
                                            </div>
                                        </div>
                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.commission_cost').' ( '.$order->commission.' % )' }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->commission_cost }} {{ $currency_sign }}
                                            </div>
                                        </div>





                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab_2">


                        </div>


                    </div>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>

<div id="invoice-content" style="display: none;"> 
    @include('reports/receipt')
</div>
<script>
    var new_lang = {

    };
    var new_config = {

    }

</script>
@endsection
