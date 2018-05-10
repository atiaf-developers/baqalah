var Contact= function () {

    var init = function () {
        handle_submit();
    }

       var handle_submit = function () {
        $("#contacts-form").validate({
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                type: {
                    required: true
                },
                message: {
                    required: true
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
       $('#contacts-form .submit-form').click(function () {
            if ($('#contacts-form').validate().form()) {
                $('#contacts-form .submit-form').prop('disabled', true);
                $('#contacts-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#contacts-form').submit();
                }, 1000);
            }
            return false;
        });
        $('#contacts-form').submit(function () {
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: config.url + "/contact-us",
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

                    $('#contacts-form .submit-form').prop('disabled', false);
                    $('#contacts-form .submit-form').html(lang.send);
                    if (data.type == 'success') {
                        $('.alert-danger').hide();
                        $('.alert-success').show().find('.message').html(data.message);
//                        setTimeout(function () {
//                            window.location.href = config.url + '/login';
//                        }, 3000);

                    } else {
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
                    $('#contacts-form .submit-form').prop('disabled', false);
                    $('#contacts-form .submit-form').html(lang.send);
                    App.ajax_error_message(xhr);

                },
            });

            return false;
        });

    }





    return{

        init: function () {
            init();
        },

    }


}();

$(document).ready(function () {
    Contact.init();
});





