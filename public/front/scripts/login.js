var currentTab = 0;
var activation_code = false;
var Login = function () {

    var init = function () {
        handle_login();
        handle_register();
        handle_forgot_password();
        handle_change_password();
        handle_activation_code();
        handle_edit_phone();
        showTab(currentTab);


    }
    var handle_login = function () {
        $("#login-form").validate({
            rules: {
                username: {
                    required: true,
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
                errorElements1.push(element);
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });
        $('#login-form .submit-form').click(function () {
            var validate_2 = $('#login-form').validate().form();
            errorElements = errorElements1.concat(errorElements2);
            if (validate_2) {
                $('#login-form .submit-form').prop('disabled', true);
                $('#login-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#login-form').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('#login-form'));
            }

            return false;
        });

        $('#login-form input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#login-form').validate().form();
                errorElements = errorElements1.concat(errorElements2);
                if (validate_2) {
                    $('#login-form .submit-form').prop('disabled', true);
                    $('#login-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#login-form').submit();
                    }, 1000);

                }
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('#login-form'));
                }

                return false;
            }
        });
        $('#login-form').submit(function () {
            var formData = new FormData($(this)[0]);
            var return_url = App.getParameterByName('return', window.location.href);
            if (return_url !== null) {
                formData.append('return', return_url);
            }
            $.ajax({
                url: config.url + "/login",
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    console.log(data);
                    if (data.type == 'success') {
                        setTimeout(function () {
                            window.location.href = config.url + '/';
                        }, 1000);


                    } else {
                        $('#login-form .submit-form').prop('disabled', false);
                        $('#login-form .submit-form').html(lang.register);
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(data.errors[i][0])
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#login-form .submit-form').prop('disabled', false);
                    $('#login-form .submit-form').html(lang.login);
                    App.ajax_error_message(xhr);
                },
            });

            return false;
        });

    }
    var handle_forgot_password = function () {
        $("#forgotPasswordForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                email: {
                    required: lang.required,
                    email: lang.email_not_valid
                },
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
        $('#forgotPasswordForm .submit-form').click(function () {
            if ($('#forgotPasswordForm').validate().form()) {
                $('#forgotPasswordForm.submit-form').prop('disabled', true);
                $('#forgotPasswordForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#forgotPasswordForm').submit();
                }, 1000);
            }
            return false;
        });

        $('#forgotPasswordForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#forgotPasswordForm').validate().form()) {
                    $('#forgotPasswordForm .submit-form').prop('disabled', true);
                    $('#forgotPasswordForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#forgotPasswordForm').submit();
                    }, 1000);
                }
                return false;
            }
        });
        $('#forgotPasswordForm').submit(function () {
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: config.url + "/password/email",
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    console.log(data);


                    if (data.type == 'success') {
                        $('.alert-danger').hide();
                        $('.alert-success').show().find('.message').html(data.message);

                    } else {
                        $('#register-form .submit-form').prop('disabled', false);
                        $('#register-form .submit-form').html(lang.register);
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(data.errors[i][0])
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#forgotPasswordForm .submit-form').prop('disabled', false);
                    $('#forgotPasswordForm .submit-form').html(lang.send_request);
                    App.ajax_error_message(xhr);

                },
            });

            return false;
        });

    }






    var handle_change_password = function () {
        $("#changePasswordForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
                },
            },
            messages: {
                password: {
                    required: lang.required
                },
                password_confirmation: {
                    required: lang.required,
                    equalTo: lang.please_enter_the_same_value_again,
                },
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
        $('#changePasswordForm .submit-form').click(function () {
            if ($('#changePasswordForm').validate().form()) {
                $('#changePasswordForm.submit-form').prop('disabled', true);
                $('#changePasswordForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#changePasswordForm').submit();
                }, 1000);
            }
            return false;
        });

        $('#changePasswordForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#changePasswordForm').validate().form()) {
                    $('#changePasswordForm .submit-form').prop('disabled', true);
                    $('#changePasswordForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#changePasswordForm').submit();
                    }, 1000);
                }
                return false;
            }
        });
        $('#changePasswordForm').submit(function () {
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: config.url + "/password/reset",
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    console.log(data);
                     if (data.type == 'success') {
//                        $('.alert-danger').hide();
//                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.url + '/login';
                        }, 3000);

                    } else {
                        $('#register-form .submit-form').prop('disabled', false);
                        $('#register-form .submit-form').html(lang.register);
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(data.errors[i][0])
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        }
                    }
                  


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#changePasswordForm .submit-form').prop('disabled', false);
                    $('#changePasswordForm .submit-form').html(lang.send);
                    App.ajax_error_message(xhr);
                },
            });

            return false;
        });

    }
    var handle_register2 = function () {
        $("#register-form").validate({
            rules: {
                username: {
                    required: true
                },
                email: {
                    required: true
                },
                password: {
                    required: true
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
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
                errorElements1.push(element);
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });
        $('#register-form .submit-form').click(function () {
            var validate_2 = $('#register-form').validate().form();
            errorElements = errorElements1.concat(errorElements2);
            if (validate_2) {
                $('#register-form .submit-form').prop('disabled', true);
                $('#register-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#register-form').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('#register-form'));
            }

            return false;

        });
        $('#register-form input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#register-form').validate().form();
                errorElements = errorElements1.concat(errorElements2);
                if (validate_2) {
                    $('#register-form .submit-form').prop('disabled', true);
                    $('#register-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#register-form').submit();
                    }, 1000);

                }
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('#register-form'));
                }

                return false;
            }
        });
        $('#register-form').submit(function () {
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: config.url + "/register",
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    console.log(data);

                    $('#register-form .submit-form').prop('disabled', false);
                    $('#register-form .submit-form').html(lang.register_now);
                    if (data.type == 'success') {
                        $('.alert-danger').hide();
                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.url + '/login';
                        }, 3000);

                    } else {
                        $('#register-form .submit-form').prop('disabled', false);
                        $('#register-form .submit-form').html(lang.register);
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(data.errors[i][0])
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#register-form .submit-form').prop('disabled', false);
                    $('#register-form .submit-form').html(lang.register);
                    App.ajax_error_message(xhr);

                },
            });

            return false;
        });

    }



    var handle_activation_code = function () {
        $("#activationForm").validate({
            rules: {
                'activation[]': {
                    required: true,
                },
            },
            messages: {
                'activation[]': {
                    required: lang.required,
                },
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
        $('#activationForm .submit-form').click(function () {
            if ($('#activationForm').validate().form()) {
                $('#activationForm.submit-form').prop('disabled', true);
                $('#activationForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#activationForm').submit();
                }, 1000);
            }
            return false;
        });

        $('#activationForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#activationForm').validate().form()) {
                    $('#activationForm .submit-form').prop('disabled', true);
                    $('#activationForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#activationForm').submit();
                    }, 1000);
                }
                return false;
            }
        });
        $('#activationForm').submit(function () {
            var form_data = new FormData($(this)[0]);
            $.ajax({
                url: config.url + "/activateuser",
                type: 'POST',
                dataType: 'json',
                data: form_data,
                processData: false,
                contentType: false,
                success: function (data)
                {
                    console.log(data);


                    if (data.type == 'success') {

                        setTimeout(function () {
                            window.location.href = data.message;
                        }, 2500);


                    } else {
                        $('#activationForm .submit-form').prop('disabled', false);
                        $('#activationForm .submit-form').html(lang.confirm);
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-info");
                                $('#' + i).closest('.form-group').find(".help-block").html(data.errors[i])
                            }
                        } else {
                            $('#alert-message').removeClass('alert-success').addClass('alert-danger').fadeIn(500).delay(3000).fadeOut(2000);
                            var message = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span>' + data.message + '</span> ';
                            $('#alert-message').html(message);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#activationForm .submit-form').prop('disabled', false);
                    $('#activationForm .submit-form').html(lang.confirm);
                    bootbox.dialog({
                        message: xhr.responseText,
                        title: 'error',
                        buttons: {
                            danger: {
                                label: 'error',
                                className: "red"
                            }
                        }
                    });
                },
            });

            return false;
        });

    }


    var handle_edit_phone = function () {
        $("#editMobileForm").validate({
            rules: {
                'mobile': {
                    required: true,
                },
            },
            messages: {
                'mobile': {
                    required: lang.required,
                },
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
        $('#editMobileForm .submit-form').click(function () {
            if ($('#editMobileForm').validate().form()) {
                $('#editMobileForm.submit-form').prop('disabled', true);
                $('#editMobileForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#editMobileForm').submit();
                }, 1000);
            }
            return false;
        });

        $('#editMobileForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#editMobileForm').validate().form()) {
                    $('#editMobileForm .submit-form').prop('disabled', true);
                    $('#editMobileForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#editMobileForm').submit();
                    }, 1000);
                }
                return false;
            }
        });
        $('#editMobileForm').submit(function () {
            var form_data = new FormData($(this)[0]);
            $.ajax({
                url: config.url + "/edituserphone",
                type: 'POST',
                dataType: 'json',
                data: form_data,
                processData: false,
                contentType: false,
                success: function (data)
                {
                    console.log(data);


                    if (data.type == 'success') {

                        setTimeout(function () {
                            window.location.href = data.message;
                        }, 2500);


                    } else {
                        $('#editMobileForm .submit-form').prop('disabled', false);
                        $('#editMobileForm .submit-form').html(lang.confirm);
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-info");
                                $('#' + i).closest('.form-group').find(".help-block").html(data.errors[i])
                            }
                        } else {
                            $('#alert-message').removeClass('alert-success').addClass('alert-danger').fadeIn(500).delay(3000).fadeOut(2000);
                            var message = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span>' + data.message + '</span> ';
                            $('#alert-message').html(message);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#editMobileForm .submit-form').prop('disabled', false);
                    $('#editMobileForm .submit-form').html(lang.confirm);
                    bootbox.dialog({
                        message: xhr.responseText,
                        title: 'error',
                        buttons: {
                            danger: {
                                label: 'close',
                                className: "red"
                            }
                        }
                    });
                },
            });

            return false;
        });

    }
      var handle_register = function () {
        $("#regForm").validate({
            //ignore: "",
            rules: {
//                name: {
//                    required: true
//                },
//                reservation_date: {
//                    required: true
//                },
//                reservation_time: {
//                    required: true
//                },
//                payment_method: {
//                    required: true
//                },
//                email: {
//                    required: true,
//                    email: true
//                },
//                phone: {
//                    required: true
//                },
//                lat: {
//                    required: true
//                },
//                lng: {
//                    required: true
//                },

            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('');

            },
            errorPlacement: function (error, element) {
                errorElements1.push(element);
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });

        $('#regForm').submit(function () {
            var formData = new FormData($(this)[0]);
            formData.append('step', currentTab + 1);
            if (activation_code) {
                formData.append('ajax_code', activation_code);
            }
            $.ajax({
                url: config.url + "/register",
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    console.log(data);


                    if (data.type == 'success') {
                        $('#nextBtn').prop('disabled', false);
                        $('#nextBtn').html(lang.next);
                        var step = data.data.step;
                        console.log(currentTab);
                        if (step == 3) {
                            $('.next2').hide();
                            $('.alert-danger').hide();
                            $('.alert-success').show().find('.message').html(data.data.message);
                        } else if (step == 1) {
                            $('#mobile-message').html($('#mobile').val());
                            activation_code = data.data.activation_code;

                        } else {

                        }
                        var hideTab = currentTab;
                        currentTab = currentTab + 1;
                        $('.tab:eq(' + hideTab + ')').hide();
                        showTab(currentTab);


//                        var hideTabe = currentTab - 1;
//                        $('.tab:eq(' + hideTabe + ')').hide();
//                        showTab(currentTab);



                    } else {
                        $('#nextBtn').prop('disabled', false);
                        $('#nextBtn').html(lang.next);

                        if (typeof data.errors !== 'undefined') {

                            for (i in data.errors)
                            {
                                var message = data.errors[i][0];
                                if (i.startsWith('code')) {
                                    var key_arr = i.split('.');
                                    var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                    i = key_text;
                                } else if (i.startsWith('activation_code')) {
                                    $('.msg-error').show();
                                    $('#activation-code-message').html(message);
                                    continue;
                                }
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message)
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        }
                    }



                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#nextBtn').prop('disabled', false);
                    $('#nextBtn').html(lang.next);
                    App.ajax_error_message(xhr);

                },
            });

            return false;
        });

    }
      var showTab = function (n) {
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        //... and fix the Previous/Next buttons:
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").innerHTML = "احجز";
        } else {
            document.getElementById("nextBtn").innerHTML = "التالى";
        }
        //... and run a function that will display the correct step indicator:
        fixStepIndicator(n)
    }

    var fixStepIndicator = function (n) {
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        //... and adds the "active" class on the current step:
        x[n].className += " active";
    }

    return {
        init: function () {
            init();
        },
        empty: function () {
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');

            App.emptyForm();
        },
          nextPrev: function (ele, n) {
            var type = $(ele).data('type');
            var x = document.getElementsByClassName("tab");
            var validate = $('#regForm').validate().form();
            if (type == 'next' && !validate) {
                return false;
            } else {
                if (type == 'next') {
                    $(ele).prop('disabled', true);
                    $(ele).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#regForm').submit();
                    }, 1000);

                    return false;
                } else {
                    var hideTab = currentTab;
                    currentTab = currentTab - 1;
                    $('.tab:eq(' + hideTab + ')').hide();
                    showTab(currentTab);
                }
            }



        }
    }

}();

jQuery(document).ready(function () {
    Login.init();
});


