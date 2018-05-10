
var Profile = function () {

    var init = function () {
        handle_edit();
//        $.notify("asdf", {
//        icon: "https://avatars3.githubusercontent.com/u/11307908?v=3&s=460"
//      });


    }

    var handle_edit = function () {
        $("#edit-form").validate({
            rules: {
//                name: {
//                    required: true
//                },
//                username: {
//                    required: true
//                },
//                mobile: {
//                    required: true
//                },
//                email: {
//                    email: true
//                },
//                password: {
//                    required: true
//                },
//                confirm_password: {
//                    required: true,
//                    equalTo: "#password"
//                }
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
        $('#edit-form .submit-form').click(function () {
            var validate_2 = $('#edit-form').validate().form();
            errorElements = errorElements1.concat(errorElements2);
            if (validate_2) {
                $('#edit-form .submit-form').prop('disabled', true);
                $('#edit-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#edit-form').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('#edit-form'));
            }

            return false;

        });
        $('#edit-form input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#edit-form').validate().form();
                errorElements = errorElements1.concat(errorElements2);
                if (validate_2) {
                    $('#edit-form .submit-form').prop('disabled', true);
                    $('#edit-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#edit-form').submit();
                    }, 1000);

                }
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('#edit-form'));
                }

                return false;
            }
        });
        $('#edit-form').submit(function () {
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: config.customer_url + "/user/edit",
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

//                    $('#edit-form .submit-form').prop('disabled', false);
//                    $('#edit-form .submit-form').html(lang.save);
                    if (data.type == 'success') {
//                        $('.alert-danger').hide();
//                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.customer_url + '/dashboard';
                        }, 3000);

                    } else {
                        $('#edit-form .submit-form').prop('disabled', false);
                        $('#edit-form .submit-form').html(lang.edit);
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
                    $('#edit-form .submit-form').prop('disabled', false);
                    $('#edit-form .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);

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
    Profile.init();
});


