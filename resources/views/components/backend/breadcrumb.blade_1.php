<!-- BEGIN PAGE BAR -->
<div class="theme-panel hidden-xs hidden-sm">
    <div class="toggler" style="display: block;">
    </div>
    <div class="toggler-close" style="display: none;">
    </div>
    <div class="theme-options" style="display: none;">
       <div class="theme-option theme-colors clearfix">
           <span>{{_lang('app.settings')}}</span>
<!--            <ul>
                <li class="color-default tooltips" data-style="default" data-container="body" data-original-title="Default">
                </li>
                <li class="color-darkblue tooltips" data-style="darkblue" data-container="body" data-original-title="Dark Blue">
                </li>
                <li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Blue">
                </li>
                <li class="color-grey tooltips current" data-style="grey" data-container="body" data-original-title="Grey">
                </li>
                <li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Light">
                </li>
                <li class="color-light2 tooltips" data-style="light2" data-container="body" data-html="true" data-original-title="Light 2">
                </li>
            </ul>-->
        </div>
        <div class="theme-option">
            <span>
                {{_lang('app.language')}} </span>
            <select class="layout-option form-control input-sm" id="change-lang">
                <option selected value="ar">{{_lang('app.arabic')}}</option>
                <option value="en">{{_lang('app.english')}}</option>
            </select>
        </div>
       
    </div>
</div>
<div class="page-bar">
    
    <ul class="page-breadcrumb">
        @yield('breadcrumb')
       
    </ul>
    <!--
        <div class="page-toolbar">
            <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                <i class="icon-calendar"></i>&nbsp;
                <span class="thin uppercase hidden-xs"></span>&nbsp;
                <i class="fa fa-angle-down"></i>
            </div>
        </div>
    -->
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->

<h1 class="page-title"> {{_lang('app.')}}
    <small></small>
</h1>
<!-- END PAGE TITLE-->
<div class="clearfix"></div>