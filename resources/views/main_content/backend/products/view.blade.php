@extends('layouts.backend')

@section('pageTitle', _lang('app.product'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/stores')}}">{{_lang('app.stores')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/products')}}/{{$product->store_id}}">{{_lang('app.products')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/product.js" type="text/javascript"></script>
@endsection
@section('content')


<div class="row">
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <div class="col-md-12">

                <!-- BEGIN SAMPLE TABLE PORTLET-->
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>{{$product->name}}
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-hover">

                                <tbody>
                                    <tr>
                                        <td>{{ _lang('app.name')}}</td>
                                        <td>{{$product->name}}</td>

                                    </tr>
                                    <tr>
                                            <td>{{ _lang('app.quantity')}}</td>
                                            <td>{{$product->quantity}}</td>
    
                                        </tr>
                                    <tr>
                                            <td>{{ _lang('app.price')}}</td>
                                            <td>{{$product->price}}</td>
    
                                        </tr>
                                    <tr>
                                        <td>{{ _lang('app.category')}}</td>
                                        <td>{{$product->category}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.description')}}</td>
                                        <td>{{$product->description}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{--  dd($product->images);  --}}
                        @if(count(json_decode($product->images))>0)
                       
                        <h3>{{_lang('app.gallery')}}</h3>
                        <ul class="list-inline blog-images">
                            @foreach(json_decode($product->images) as $one)
                            <li>
                                <a class="fancybox-button" product-rel="fancybox-button" title="390 x 220 - keenthemes.com" href="{{url('public/uploads/products')}}/{{$one}}">
                                    <img style="width: 100px;height: 100px;" alt="" src="{{url('public/uploads/products')}}/{{$one}}">
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        @endif
                    </div>
                </div>
                <!-- END SAMPLE TABLE PORTLET-->


            </div>
        </div>
      
    </div>
    <div class="row">










    </div>


</div>


<script>
var new_lang = {

};

</script>
@endsection
