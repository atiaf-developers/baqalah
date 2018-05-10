var type = 2;
var Famous_grid;
var Members_grid;
var Worker_grid;
var Worker = function() {
    var init = function() {
        //alert('heree');
        $.extend(lang, new_lang);
        
        //console.log(lang);
        handleRecords();
        handleDatatables();
        handleSubmit();
        handlePasswordActions();
        My.readImageMulti('user_image');

    };
    var handlePasswordActions = function(string_length) {
        $('#show-password').click(function() {
            if ($('#password').val() != '') {
                $("#password").attr("type", "text");
            } else {
                $("#password").attr("type", "password");
            }
        });
        $('#random-password').click(function() {
            $('[id^="password"]').closest('.form-group').removeClass('has-error').addClass('has-success');
            $('[id^="password"]').closest('.form-group').find('.help-block').html('').css('opacity', 0);
            $('[id^="password"]').val(randomPassword(8));
        });
    }
    var randomPassword = function(string_length) {
        var chars = "0123456789!@#$%^&*abcdefghijklmnopqrstuvwxtzABCDEFGHIJKLMNOPQRSTUVWXTZ!@#$%^&*";
        var myrnd = [],
            pos;
        while (string_length--) {
            pos = Math.floor(Math.random() * chars.length);
            myrnd += chars.substr(pos, 1);
        }
        return myrnd;
    }

    var handleDatatables = function() {
        $(document).on('click', '.data-box', function() {
            $('.data-box').removeClass('active');
            $(this).addClass('active');
            type = 2;
            $('.table-container').hide();
            if (type == 2) {

                if (typeof Famous_grid === 'undefined') {
                    Famous_grid = $('#famous-table .dataTable').dataTable({
                        //"processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": config.admin_url + "/users/data?type=clients",
                            "type": "POST",
                            data: { type: type, _token: $('input[name="_token"]').val() },
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

                } else {
                    Famous_grid.on('preXhr.dt', function(e, settings, data) {
                        data.type = type
                        data._token = $('input[name="_token"]').val()
                    })
                    Famous_grid.api().ajax.url(config.admin_url + "/users/data?type=clients").load();
                }
                $('#famous-table').show();
            } else {
                if (typeof Members_grid === 'undefined') {

                    Members_grid = $('#members-table .dataTable').dataTable({
                        //"processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": config.admin_url + "/users/data?type=clients",
                            "type": "POST",
                            data: { type: type, _token: $('input[name="_token"]').val() },
                        },
                        "columns": [
                            //                    {"data": "user_input", orderable: false, "class": "text-center"},
                            { "data": "name", "name": "name" },
                            { "data": "username", "name": "username" },
                            { "data": "mobile", "name": "mobile" },
                            { "data": "active", "name": "active" },
                        ],
                        "order": [
                            [1, "desc"]
                        ],
                        "oLanguage": { "sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json' }

                    });

                } else {
                    Members_grid.on('preXhr.dt', function(e, settings, data) {
                        data.type = type
                        data._token = $('input[name="_token"]').val()
                    })
                    Members_grid.api().ajax.url(config.admin_url + "/users/data?type=clients").load();
                }
                $('#members-table').show();
            }




            return false;
        });
    }
    var handleRecords = function() {

        Famous_grid = $('#famous-table .dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/users/data?type=clients",
                "type": "POST",
                data: { type: type, _token: $('input[name="_token"]').val() },
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

        $('#addEditWorkerForm').validate({
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
        $('#addEditWorker .submit-form').click(function() {
            if ($('#addEditWorkerForm').validate().form()) {
                $('#addEditWorker .submit-form').prop('disabled', true);
                $('#addEditWorker .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function() {
                    $('#addEditWorkerForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditWorkerForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#addEditWorkerForm').validate().form()) {
                    $('#addEditWorker .submit-form').prop('disabled', true);
                    $('#addEditWorker .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function() {
                        $('#addEditWorkerForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditWorkerForm').submit(function() {
            var id = $('#id').val();
            var formData = new FormData($(this)[0]);
            var action = config.admin_url + '/users?type=clients';
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/users/' + id + '?type=clients';
            }


            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#addEditWorker .submit-form').prop('disabled', false);
                    $('#addEditWorker .submit-form').html(lang.save);

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
                            $('#addEditWorker').modal('hide');
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
                    $('#addEditWorker .submit-form').prop('disabled', false);
                    $('#addEditWorker .submit-form').html(lang.save);
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
                url: config.admin_url + '/users/' + id + '?type=delegates',
                success: function(data) {
                    console.log(data);

                    Worker.empty();
                    My.setModalTitle('#addEditWorker', lang.edit_user);

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

                    $('#addEditWorker').modal('show');
                   
                }
            });

        },
        massage: function(t) {
            var id = $(t).attr("data-id");
            document.getElementById('user_id').value = id;
            My.editForm({
                element: t,
                url: config.admin_url + '/users/' + id + '?type=clients',
                success: function(data) {
                    console.log(data);

                    Worker.empty();
                    My.setModalTitle('#addEditWorker', lang.edit_user);

                    for (i in data.message) {
                        if (i == 'password') {
                            continue;
                        } else if (i == 'user_image') {
                            $('.user_image_box').html('<img style="height:80px;width:150px;" class="user_image"  src="' + config.public_path + '/uploads/users/' + data.message[i] + '" alt="your image" />');
                        } else {
                            $('#' + i).val(data.message[i]);
                        }
                    }

                    $('#sendMassage').modal('show');
                }
            });

        },
        Orders: function(t) {
            var id = $(t).attr("data-id");
            var action = config.admin_url + '/order/wait/' + id;
            $.ajax({
                url: action,
                data: '',
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
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

                        // if (id != 0) {
                        //     $('#addEditUsers').modal('hide');
                        // } else {
                        //     Users.empty();
                        // }

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

                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "GET"
            });

            return false;
        },
        allowable: function(t) {
            var id = $(t).attr("data-id");

            var action = config.admin_url + '/users/trust/' + id + '?type=clients';
            $.ajax({
                url: action,
                data: '',
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
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

                        // if (id != 0) {
                        //     $('#addEditUsers').modal('hide');
                        // } else {
                        //     Users.empty();
                        // }

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

                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "GET"
            });

            return false;
        },
        order: function(t) {
            var id = $(t).attr("data-id");

            var action = config.admin_url + '/users/order/' + id + '?type=clients';
            $.ajax({
                url: action,
                data: '',
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
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

                        // if (id != 0) {
                        //     $('#addEditUsers').modal('hide');
                        // } else {
                        //     Users.empty();
                        // }

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

                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "GET"
            });

            return false;
        },
        delete: function(t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/users/' + id + '?type=delegates',
                data: { _method: 'DELETE', _token: $('input[name="_token"]').val() },
                success: function(data) {

                    Famous_grid.api().ajax.reload();


                }
            });
        },
        add: function() {
            // google.maps.event.trigger(map, "resize");
            Worker.empty();
            My.setModalTitle('#addEditWorker', lang.add_user);
            // console.log(google.maps.event.trigger);
            $('#addEditWorker').modal('show');

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
    Worker.init();
});