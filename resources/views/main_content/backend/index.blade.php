@extends('layouts.backend')

@section('pageTitle', _lang('app.dashboard'))



@section('content')
<div class="row" style="margin-top: 40px;">
   
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-madison"  href="">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{$counts['clients']['count']}}">{{$counts['clients']['count']}}</span>
                </div>
                <div class="desc">{{_lang('app.clients')}}</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-haze" href="">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{$counts['stores']['count']}}">{{$counts['stores']['count']}}</span>
                </div>
                <div class="desc">{{_lang('app.stores')}}</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple-plum" href="">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{$counts['orders_in_proccessing']['count']}}">{{$counts['orders_in_proccessing']['count']}}</span>
                </div>
                <div class="desc">{{_lang('app.orders_in_proccessing')}}</div>
            </div>
        </a>
    </div>
  
    
<!--    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red-flamingo" href="http://wsool.co/admin/companies">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="2">2</span>
                </div>
                <div class="desc">الشركات الخدمية</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-haze" href="http://wsool.co/admin/companies">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="18">18</span>
                </div>
                <div class="desc">الشركات الصناعية</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-haze" href="http://wsool.co/admin/companies">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="18">18</span>
                </div>
                <div class="desc">الشركات الصناعية</div>
            </div>
        </a>
    </div>-->

    


</div>

@endsection