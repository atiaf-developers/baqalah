var Admins_grid;
var Admins = function () {
    var init = function () {
        //alert('heree');
        $.extend(lang, new_lang);
        //console.log(lang);
        handleRecords();
        handleSubmit();
        handlePasswordActions();

    };
    var handlePasswordActions = function (string_length) {
        $('#show-password').click(function () {
            if ($('#password').val() != '') {
                $("#password").attr("type", "text");

            } else {
                $("#password").attr("type", "password");

            }
        });
        $('#random-password').click(function () {
            $('[id^="password"]').closest('.form-group').removeClass('has-error').addClass('has-success');
            $('[id^="password"]').closest('.form-group').find('.help-block').html('').css('opacity', 0);
            $('[id^="password"]').val(randomPassword(8));
        });
    }
    var randomPassword = function (string_length) {
        var chars = "0123456789!@#$%^&*abcdefghijklmnopqrstuvwxtzABCDEFGHIJKLMNOPQRSTUVWXTZ!@#$%^&*";
        var myrnd = [], pos;
        while (string_length--) {
            pos = Math.floor(Math.random() * chars.length);
            myrnd += chars.substr(pos, 1);
        }
        return myrnd;
    }
    var handleRecords = function () {

        Admins_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/admins/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "username"},
                {"data": "group.name"},
                {"data": "active"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [1, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }
    var handleSubmit = function () {

        $('#addEditAdminsForm').validate({
            rules: {
                username: {
                    required: true

                },

                group_id: {
                    required: true

                },
                phone: {
                    required: true,
                    number: true
                },
                email: {
                    required: true,
                    email: true,
                },
            },
            //messages: lang.messages,
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);

            },
            errorPlacement: function (error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });
        $('#addEditAdmins .submit-form').click(function () {
            if ($('#addEditAdminsForm').validate().form()) {
                $('#addEditAdmins .submit-form').prop('disabled', true);
                $('#addEditAdmins .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditAdminsForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditAdminsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditAdminsForm').validate().form()) {
                    $('#addEditAdmins .submit-form').prop('disabled', true);
                    $('#addEditAdmins .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditAdminsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditAdminsForm').submit(function () {
            var id = $('#id').val();
            var formData = new FormData($(this)[0]);
            var action = config.admin_url + '/admins';
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/admins/' + id;
            }


            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditAdmins .submit-form').prop('disabled', false);
                    $('#addEditAdmins .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
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
                        Admins_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#addEditAdmins').modal('hide');
                        } else {
                            Admins.empty();
                        }

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
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
                                        action: function () {
                                        }
                                    }
                                }
                            });
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditAdmins .submit-form').prop('disabled', false);
                    $('#addEditAdmins .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }



    return{
        init: function () {
            init();
        },
        edit: function (t) {
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/admins/' + id,
                success: function (data)
                {
                    console.log(data);

                    Admins.empty();
                    My.setModalTitle('#addEditAdmins', lang.edit_admin);

                    for (i in data.message)
                    {
                        if (i == 'password') {
                            continue;
                        }

                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditAdmins').modal('show');
                }
            });

        },
        delete: function (t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/admins/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {

                    Admins_grid.api().ajax.reload();


                }
            });
        },
        add: function () {
            Admins.empty();
            My.setModalTitle('#addEditAdmins', lang.add_admin);
            $('#addEditAdmins').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        },
    };
}();
$(document).ready(function () {
    Admins.init();
});