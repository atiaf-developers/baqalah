@extends('layouts.backend')

@section('pageTitle', _lang('app.dashboard'))

@section('breadcrumb')
<li>
    <a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a>
</li>
@endsection

@section('content')
<!--<div class="row" style="margin-top: 40px;">

    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="http://wsool.co/admin/clients">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="28">28</span>
                </div>
                <div class="desc">العملاء</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
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
    </div>




</div>-->

@endsection