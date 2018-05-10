<meta charset="utf-8" />
<title>@yield('pageTitle')</title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta content="Markat" name="description" />
<meta content="Markat" name="author" />
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
@if($lang_code == 'ar') 
<link href="{{url('public/backend/plugins')}}/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />
@else
<link href="{{url('public/backend/plugins')}}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
@endif
@if($lang_code == 'ar') 
<link href="{{url('public/backend/plugins')}}/bootstrap-switch/css/bootstrap-switch-rtl.min.css" rel="stylesheet" type="text/css" />
@else
<link href="{{url('public/backend/plugins')}}/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
@endif
<link href="{{url('public/backend/plugins')}}/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
<link href="{{url('public/backend/plugins')}}/jquery-confirm/css/jquery-confirm.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
@if($lang_code == 'ar') 
<link href="{{url('public/backend/plugins')}}/datatables/plugins/bootstrap/datatables.bootstrap-rtl.css" rel="stylesheet" type="text/css" />
@else
<link href="{{url('public/backend/plugins')}}/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
@endif
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{url('public/backend/plugins')}}/jstree/dist/themes/default/style.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/morris/morris.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/plugins')}}/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL STYLES -->
@if($lang_code == 'ar') 
<link href="{{url('public/backend/css')}}/components-md-rtl.css" rel="stylesheet" id="style_components" type="text/css" />
<link href="{{url('public/backend/css')}}/style-rtl.css" rel="stylesheet" type="text/css" />
@else
<link href="{{url('public/backend/css')}}/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
@endif
@if($lang_code == 'ar') 
<link href="{{url('public/backend/css')}}/plugins-md-rtl.css" rel="stylesheet" type="text/css" />
@else
<link href="{{url('public/backend/css')}}/plugins-md.min.css" rel="stylesheet" type="text/css" />
@endif
<!-- END THEME GLOBAL STYLES -->
<!-- BEGIN THEME LAYOUT STYLES -->
@if($lang_code == 'ar') 
<link href="{{url('public/backend/css')}}/layout-rtl.min.css" rel="stylesheet" type="text/css" />
@else
<link href="{{url('public/backend/css')}}/layout.min.css" rel="stylesheet" type="text/css" />
@endif
@if($lang_code == 'ar') 
<link href="{{url('public/backend/css')}}/darkblue-rtl.min.css" rel="stylesheet" type="text/css" id="style_color" />
@else
<link href="{{url('public/backend/css')}}/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
@endif
<link href="{{url('public/backend/css')}}/custom.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('public/backend/css')}}/my.css" rel="stylesheet" type="text/css" />
<!-- END THEME LAYOUT STYLES -->
<link rel="shortcut icon" href="{{url('public/backend/images')}}/favicon.png" />
<link href="{{url('public/backend/plugins')}}/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
@if ($lang_code == 'ar')
    <style>
     .datetimepicker {
         float: right
      }
     .datetimepicker.dropdown-menu {
        right:auto
      }
    </style>
@endif




<script>
    var config = {
        url: "{{url('')}}",
        admin_url: " {{ url('admin')}}",
        asset_url: " {{ url('public//')}}",
        public_path: " {{ url('public//')}}",
        lang_code: "{{$lang_code}}",
        languages: '{!!json_encode(array_keys($languages))!!}',
    }
    var lang = {
        filesize_can_not_be_more_than: "{{ _lang('app.filesize_can_not_be_more_than')}}",
        lounge_number: "{{ _lang('app.lounge_number')}}",
        gender: "{{ _lang('app.gender')}}",
        male: "{{ _lang('app.male')}}",
        female: "{{ _lang('app.female')}}",
        available_of_accommodation: "{{ _lang('app.available_of_accommodation')}}",
        import: "{{ _lang('app.import')}}",
        add: "{{ _lang('app.add')}}",
        edit: "{{ _lang('app.edit')}}",
        save: "{{ _lang('app.save')}}",
        notify: "{{ _lang('app.notify')}}",
        remove: "{{ _lang('app.remove')}}",
        choose: "{{ _lang('app.choose')}}",
        delete: "{{ _lang('app.delete')}}",
        message: "{{ _lang('app.message')}}",
        view_message: "{{ _lang('app.view_message')}}",
        send: "{{ _lang('app.send')}}",
        no_results: "{{ _lang('app.no_results')}}",
        cancel_change_category: "{{ _lang('app.cancel_change_category')}}",
        change_category: "{{ _lang('app.change_category')}}",
        countries: "{{ _lang('app.countries')}}",
        no_category_selected: "{{ _lang('app.no_category_selected')}}",
        active: "{{ _lang('app.active')}}",
        not_active: "{{ _lang('app.not_active')}}",
        close: "{{ _lang('app.close')}}",
        no_item_selected: "{{ _lang('app.no_item_selected')}}",
        add_user: "{{ _lang('app.add_user')}}",
        edit_user: "{{ _lang('app.edit_user')}}",
        add_group: "{{ _lang('app.add_group')}}",
        edit_group: "{{ _lang('app.edit_group')}}",
        add_country: "{{ _lang('app.add_country')}}",
        edit_country: "{{ _lang('app.edit_country')}}",
        add_company: "{{ _lang('app.add_company')}}",
        edit_company: "{{ _lang('app.edit_company')}}",
        add_program_category: "{{ _lang('app.add_program_category')}}",
        edit_program_category: "{{ _lang('app.edit_program_category')}}",
        add_airlines: "{{ _lang('app.add_airlines')}}",
        edit_airlines: "{{ _lang('app.edit_airlines')}}",
        add_flight_bookings: "{{ _lang('app.add_flight_bookings')}}",
        edit_flight_bookings: "{{ _lang('app.edit_flight_bookings')}}",
        add_hotel_room: "{{ _lang('app.add_hotel_room')}}",
        edit_hotel_room: "{{ _lang('app.edit_hotel_room')}}",
        add_hotel_advantages: "{{ _lang('app.add_hotel_advantages')}}",
        edit_hotel_advantages: "{{ _lang('app.edit_hotel_advantages')}}",
        add_hotel_extra_services: "{{ _lang('app.add_hotel_extra_services')}}",
        edit_hotel_extra_services: "{{ _lang('app.edit_hotel_extra_services')}}",
        add_hotel_room_meals: "{{ _lang('app.add_hotel_room_meals')}}",
        edit_hotel_room_meals: "{{ _lang('app.edit_hotel_room_meals')}}",
        add_hotel: "{{ _lang('app.add_hotel')}}",
        edit_hotel: "{{ _lang('app.edit_hotel')}}",
        add_currency: "{{ _lang('app.add_currency')}}",
        edit_currency: "{{ _lang('app.edit_currency')}}",
        save: "{{ _lang('app.save')}}",
        updated_successfully: "{{ _lang('app.updated_successfully')}}",
        loading: "{{ _lang('app.loading')}}",
        deleting: "{{ _lang('app.deleting')}}",
        delete: "{{ _lang('app.delete')}}",
        uploading: "{{ _lang('app.uploading')}}",
        upload: "{{ _lang('app.upload')}}",
        required: "{{ _lang('app.this_field_is_required')}}",
        email_not_valid: "{{ _lang('app.email_is_not_valid')}}",
        alert_message: "{{ _lang('app.alert_message')}}",
        confirm_message_title: "{{ _lang('app.are you sure !?')}}",
        deleting_cancelled: "{{ _lang('app.deleting_cancelled')}}",
        yes: "{{ _lang('app.yes')}}",
        no: "{{ _lang('app.no')}}",
        error: "{{ _lang('app.error')}}",
        try_again: "{{ _lang('app.try_again')}}",
        choose_one: "{{ _lang('app.please_choose_one')}}",
        no_file_to_upload: "{{ _lang('app.no_file_to_upload')}}",
    };
    // alert(config.lang);
</script>