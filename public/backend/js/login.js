
    var errors = [];
    var Login = function () {

        var init = function () {

            handle_login();


        }
        var handle_login = function () {
            $("#login-form").validate({
                rules: {
                    username: {
                        required: true,
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    username: {
                        required: lang.required
                    },
                    password: {
                        required: lang.required
                    }
                },
                highlight: function (element) { // hightlight error inputs
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                    $(element).closest('.form-group').find('.help-block').html('');

                },
                errorPlacement: function (error, element) {
                    $(element).closest('.form-group').find('.help-block').html($(error).html());
                }

            });
            $('#login-form input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#login-form').validate().form()) {
                        $('#login-form').submit();
                    }
                    return false;
                }
            });
            $('.submit-form').click(function () {
                //alert('33333');
                if ($('#login-form').validate().form()) {
                    $('#login-form').submit();
                }
                return false;
            });
            $('#login-form').submit(function () {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: config.admin_url + "/login",
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        username: $('#username').val(),
                        password: $('#password').val(),
                        _token:_token

                    },
                    success: function (data)
                    {
                        console.log(data);
                        //return false;
                        if (data.type == 'success') {
                            window.location.href = config.admin_url;
                        } else {
                            if (typeof data.errors !== 'undefined') {
                                for (i in data.errors)
                                {
                                    $('[name="' + i + '"]')
                                            .closest('.form-group').addClass('has-error').removeClass("has-info");
                                    $('#' + i).closest('.form-group').find(".help-block").html(data.errors[i][0])
                                }
                            }
                            if (typeof data.message !== 'undefined') {
                                $('#alert-message').html(data.message).fadeIn(400).delay(3000).fadeOut(400);
                            }
                        }


                    },
                    error: function (xhr, textStatus, errorThrown) {
                        My.ajax_error_message(xhr);
   
                    },
                });

                return false;
            });

        }

        return {
            init: function () {
                init();
            }
        }

    }();

    jQuery(document).ready(function () {
        Login.init();
    });


