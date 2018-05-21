@extends('layouts.backend')

@section('pageTitle', _lang('app.Products'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/products')}}">{{_lang('app.products')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.products')}}</span></li>

@endsection

@section('js')

<script src="{{url('public/backend/js')}}/product.js" type="text/javascript"></script>

@endsection
@section('content')


{{ csrf_field() }}
<div class = "panel panel-default">
    <div class = "panel-heading">
        <h3 class = "panel-title">{{_lang('app.product')}}</h3>
    </div>
    <div class = "panel-body">
        <!--Table Wrapper Start-->
        <div class="clearfix"></div>

        <div id="famous-table" class="table-container">
           {{--  <a class = "btn btn-sm btn-info pull-left" style = "margin-bottom: 40px;" href = "" onclick = "client.add(); return false;" > {{_lang('app.add_new')}} </a>   --}}
            <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
                <thead>
                    <tr>
                        <th>{{_lang('app.title')}}</th>
                        <th>{{_lang('app.image')}}</th>
                        <th>{{_lang('app.price')}}</th>
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
    var store_id={{ $store_id }};
    var new_lang = {
        'add_client': "{{_lang('app.add_client')}}",
        'edit_client': "{{_lang('app.edit_client')}}",
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
