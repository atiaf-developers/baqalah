@extends('layouts.backend')

@section('pageTitle')
{{_lang('app.resturant_orders') }}
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/resturant_orders.js" type="text/javascript"></script>
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
                                {{ _lang('app.detailes') }} </a>
                        </li>
                        @if($User->type==2)
                        <li class="">
                            <a href="#tab_2" data-toggle="tab" aria-expanded="true">
                                {{ _lang('app.order_status') }} </a>
                        </li>
                        @endif


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
                                                    {{$order->client}}
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
                                                    {{_lang('app.city')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->city_title}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.region')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->region_title}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.payment_method')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->payment_method}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.total_cost')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->total_cost}}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 col-sm-12">
                                    <div class="portlet blue-hoki box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>{{_lang('app.address')}}
                                            </div>

                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.city')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->city}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.region')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->region}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.sub_region')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->sub_region}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.street')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->street}}
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.building_number')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->building_number}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.floor_number')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->floor_number}}
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    {{_lang('app.apartment_number')}}
                                                </div>
                                                <div class="col-md-7 value">
                                                    {{$order->apartment_number}}
                                                </div>
                                            </div>

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
                                                            <th> {{_lang('app.meal')}}</th>
                                                            <th> {{_lang('app.price')}}</th>
                                                            <th> {{_lang('app.quantity')}}</th>
                                                            <th> {{_lang('app.total_price')}}</th>


                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($meals as $key1=> $meal)
                                                        <tr>
                                                            <td> {{$key1+1}} </td>
                                                            <td>
                                                                <h5>{{$meal->title}}</h5>
                                                                @foreach($meal->sub_choices as $choice)
                                                                <p>{{$choice->title}}</p>
                                                                @endforeach
                                                            </td>
                                                            <td>{{$meal->cost_of_meal+$meal->sub_choices_price}}</td>
                                                            <td>{{$meal->quantity}}</td>
                                                            <td>{{$meal->cost_of_quantity}}</td>
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
                                                {{ _lang('app.primary_price') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->primary_price }} {{ $currency_sign }}
                                            </div>
                                        </div>




                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.service_charge').' ( '.$order->service_charge.' % )'  }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->service_charge }} %
                                            </div>
                                        </div>

                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.vat') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->vat }} %
                                            </div>
                                        </div>

                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.delivery_cost') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->delivery_cost }} {{ $currency_sign }}
                                            </div>
                                        </div>

                                        @if ($order->coupon)
                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.coupon') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->coupon }}
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.total_cost') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{ $order->total_cost }} {{ $currency_sign }}
                                            </div>
                                        </div>
                                        <div class="row static-info align-reverse">
                                            <div class="col-md-8 name">
                                                {{ _lang('app.commission') }}
                                            </div>
                                            <div class="col-md-3 value">
                                                {{( $order->total_cost*$order->commission)/100 }} {{ $currency_sign }}
                                            </div>
                                        </div>



                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($User->type==2)
                        <div class="tab-pane" id="tab_2">

                            <div class="row">
                                <form action="{{ route('order_status') }}" method="post" id="orderStatusForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="order_id" value="{{ Crypt::encrypt($order->id) }}">
                                    <div class="form-group form-md-line-input col-md-3">



                                        <?php
                                        $current_date = date('Y-m-d H:i:s');
                                        $acceptance_date = $order->acceptance_date;

                                        $to_time = strtotime($current_date);
                                        $from_time = strtotime($acceptance_date);

                                        $minutes_diff = round(abs($to_time - $from_time) / 60);

                                        $count_down = false;
                                        $diff = 3 - $minutes_diff;
                                        if ($order->status == 1 && $diff > 0 && $order->modified == 0) {
                                            $count_down = true;
                                        }
                                        ?>

                                        @if ($count_down == true)
                                        <p>{{ _lang('app.still_remaining').' '.$diff.' '._lang('app.munites_available_for_the_client_to_edit_or_cancel_this_order') }}</p>

                                        @elseif ($order->status != 4 && $order->status != 5 && $order->status != 6)


                                        <select class="table-group-action-input form-control col-md-12" name="order_status" id="order_status">

                                            @if ($order->status == 0)

                                            <option value="" >{{ _lang('app.choose') }}</option>
                                            <option value="1" {{ $order->status == 1 ? 'selected' : '' }}>{{ _lang('app.accept') }}</option>
                                            <option value="4" {{ $order->status == 4 ? 'selected' : '' }}>{{ _lang('app.reject') }}</option>

                                            @elseif($order->status != 5 && $order->status != 4 ) 

                                            <option value="" >{{ _lang('app.choose') }}</option>
                                            <option value="2" {{ $order->status == 2 ? 'selected' : '' }}>{{ _lang('app.order_is_being_delivered') }}</option>

                                            <option value="3" {{ $order->status == 3 ? 'selected' : '' }}>{{ _lang('app.order_was_deliverd') }}</option>

                                            @endif

                                        </select>
                                        <label for="order_status"> {{ _lang('order_status') }} </label>




                                    </div>

                                    <div class="form-group form-md-line-input col-md-6" id="refusing" style="display: none;">
                                        <textarea rows="5" class="form-control" id="refusing_reason" name="refusing_reason">{{ $order->status == 4 && $order->refusing_reason ? $order->refusing_reason : '' }}</textarea>
                                        <label for="refusing_reason">{{_lang('app.refusing_reason') }}</label>
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="col-md-3">
                                        <button class="submit-form btn btn-sm yellow" type="submit"><i class="fa fa-check"></i> {{ _lang('app.change') }}</button>

                                    </div>



                                    @endif

                                </form>

                                @if ($order->status == 4)
                                <h3>{{ _lang('app.refusing_reason') }}</h3>
                                <p>{{ $order->refusing_reason }}</p>
                                @endif

                                @if ($order->status == 5)
                                <h3>{{ _lang('app.under_modification') }}</h3>
                                <p>{{ $order->refusing_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>

<div id="invoice-content" style="display: none;"> 
    @include('main_content/reports/receipt')
</div>
<script>
    var new_lang = {

    };
    var new_config = {

    }

</script>
@endsection
