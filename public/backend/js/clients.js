
var Clients_grid;

var Clients = function() {
    var init = function() {
        //alert('heree');
        $.extend(lang, new_lang);
        //console.log(lang);
        handleRecords();
        handleSubmit();
        My.readImageMulti('user_image');

    };
   

    
    var handleRecords = function() {

        Clients_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/clients/data",
                "type": "POST",
                data: { _token: $('input[name="_token"]').val() },
            },
            "columns": [
                //                    {"data": "user_input", orderable: false, "class": "text-center"},
                { "data": "username", "name": "username" },
                { "data": "image", "name": "image" },
                { "data": "mobile", "name": "mobile" },
                { "data": "active", "name": "active" },
                { "data": "options", orderable: false, searchable: false }
            ],
            "order": [
                [1, "desc"]
            ],
            "oLanguage": { "sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json' }

        });
    }
    var handleSubmit = function() {

        $('#addEditClientForm').validate({
            rules: {
                username: {
                    required: true
                },
                mobile: {
                    required: true
                },

            },
            //messages: lang.messages,
            highlight: function(element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);

            },
            errorPlacement: function(error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });
        $('#addEditClient .submit-form').click(function() {
            if ($('#addEditClientForm').validate().form()) {
                $('#addEditClient .submit-form').prop('disabled', true);
                $('#addEditClient .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function() {
                    $('#addEditClientForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditClientForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#addEditClientForm').validate().form()) {
                    $('#addEditClient .submit-form').prop('disabled', true);
                    $('#addEditClient .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function() {
                        $('#addEditClientForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditClientForm').submit(function() {
            var id = $('#id').val();
            var formData = new FormData($(this)[0]);
            var action = config.admin_url + '/clients';
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/clients/' + id + '';
            }


            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#addEditClient .submit-form').prop('disabled', false);
                    $('#addEditClient .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        toastr.options = {
                            "debug": false,
                            "positionClass": "toast-bottom-left",
                            "onclick": null,
                            "fadeIn": 300,
                            "fadeOut": 1000,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000,
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };
                        toastr.success(data.message, 'رسالة');
                        Famous_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#addEditClient').modal('hide');
                        } else {
                            Worker.empty();
                        }

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors) {
                                $('[name="' + i + '"]')
                                    .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(data.errors[i][0]).css('opacity', 1)
                            }
                        } else {
                            //alert('here');
                            $.confirm({
                                title: lang.error,
                                content: data.message,
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: lang.try_again,
                                        btnClass: 'btn-red',
                                        action: function() {}
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#addEditClient .submit-form').prop('disabled', false);
                    $('#addEditClient .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        });
        $('#sendMassageForm').submit(function() {
            var id = $('#id').val();
            var formData = new FormData($(this)[0]);
            var action = config.admin_url + '/msgUsers?page=client';
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#addEditUsers .submit-form').prop('disabled', false);
                    $('#addEditUsers .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        toastr.options = {
                            "debug": false,
                            "positionClass": "toast-bottom-left",
                            "onclick": null,
                            "fadeIn": 300,
                            "fadeOut": 1000,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000,
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };
                        toastr.success(data.message, 'رسالة');
                        Famous_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#sendMassage').modal('hide');
                        } else {
                            Users.empty();
                        }

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors) {
                                $('[name="' + i + '"]')
                                    .closest('.form-group').addClass('has-error');
                                $('#' + i).closest('.form-group').find(".help-block").html(data.errors[i][0]).css('opacity', 1)
                            }
                        } else {
                            //alert('here');
                            $.confirm({
                                title: lang.error,
                                content: data.message,
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: lang.try_again,
                                        btnClass: 'btn-red',
                                        action: function() {}
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#sendMassageForm .submit-form').prop('disabled', false);
                    $('#sendMassageForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        });




    }



    return {
        init: function() {
            init();
        },
        edit: function(t) {
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/clients/' + id + '',
                success: function(data) {
                    console.log(data);

                    Clients.empty();
                    My.setModalTitle('#addEditClient', lang.edit_user);

                    for (i in data.message) {
                        if (i == 'password') {
                            continue;
                        } else if (i == 'image') {
                            if (!data.message[i]) {
                                data.message[i] = 'default.png'
                            }
                            $('.user_image_box').html('<img style="height:80px;width:150px;" class="user_image"  src="' + config.public_path + '/uploads/users/' + data.message[i] + '" alt="your image" />');
                        } else {
                            $('#' + i).val(data.message[i]);
                        }
                    }

                    $('#addEditClient').modal('show');

                }
            });

        },
      
     
       status: function(t) {
            var client_id = $(t).data("id"); 
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');

            $.ajax({
                url: config.admin_url+'/clients/status/'+client_id,
                success: function(data){  
                     $(t).prop('disabled', false);
                     if ($(t).hasClass( "btn-info" )) {
                        $(t).addClass('btn-danger').removeClass('btn-info');
                        $(t).html(lang.not_active);

                    }else{
                        $(t).addClass('btn-info').removeClass('btn-danger');
                        $(t).html(lang.active);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                   My.ajax_error_message(xhr);
               },
            });

        },
        delete: function(t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/clients/' + id + '',
                data: { _method: 'DELETE', _token: $('input[name="_token"]').val() },
                success: function(data) {

                    Famous_grid.api().ajax.reload();


                }
            });
        },
        add: function() {
            // google.maps.event.trigger(map, "resize");
            Clients.empty();
            My.setModalTitle('#addEditClient', lang.add_user);
            // console.log(google.maps.event.trigger);
            $('#addEditClient').modal('show');

            // initMap();


        },
        empty: function() {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('#user_image').val(null);
            $('.user_image_box').html('<img src="' + config.url + '/no-image.png" class="user_image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        },
    };
}();
$(document).ready(function() {
    Clients.init();
});