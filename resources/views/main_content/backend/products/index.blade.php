@extends('layouts.backend')

@section('pageTitle', _lang('app.Products'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>

@if (isset($store_name))
    <li><a href="{{url('admin/stores')}}">{{_lang('app.stores')}}</a> <i class="fa fa-circle"></i></li>
    <li><span>{{ $store_name }} </span> <i class="fa fa-circle"></i></li>
@endif
@if (isset($category_name))
    <li><a href="{{url('admin/categories')}}">{{_lang('app.categories')}}</a> <i class="fa fa-circle"></i></li>
    <li><span>{{ $category_name }} </span> <i class="fa fa-circle"></i></li>
@endif
<li> <span> {{_lang('app.products')}}</span></li>



@endsection

@section('js')

<script src="{{url('public/backend/js')}}/product.js" type="text/javascript"></script>

@endsection
@section('content')


{{ csrf_field() }}
<div class = "panel panel-default">
    
    <div class = "panel-body">
        <!--Table Wrapper Start-->
        <div class="clearfix"></div>

        <div id="" class="table-container">
           {{--  <a class = "btn btn-sm btn-info pull-left" style = "margin-bottom: 40px;" href = "" onclick = "client.add(); return false;" > {{_lang('app.add_new')}} </a>   --}}
            <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
                <thead>
                    <tr>
                        <th>{{_lang('app.product_name')}}</th>
                        <th>{{_lang('app.image')}}</th>
                        <th>{{_lang('app.price')}}</th>
                        <th>{{_lang('app.category')}}</th>
                        <th>{{_lang('app.store')}}</th>
                        <th>{{_lang('app.status')}}</th>
                        <th>{{_lang('app.options')}}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>



        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
   
    var new_lang = {
       
    };
    var new_config = {
        store_id : '{{ $store_id }}',
        category_id : '{{ $category_id }}'
    };
</script>

@endsection
