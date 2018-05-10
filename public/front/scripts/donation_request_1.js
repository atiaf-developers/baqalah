
var DonationRequest = function () {

    var init = function () {
        handle_submit();
        $(document).ready(function () {
            $('#appropriate_time').dateTimePicker();
        })

    }

    var handle_submit = function () {
        $("#donation-request-form").validate({
            ignore: "",
            rules: {
//                donation_type: {
//                    required: true
//                },
//                name: {
//                    required: true
//                },
//                mobile: {
//                    required: true
//                },
//                lat: {
//                    required: true
//                },
//                lng: {
//                    required: true
//                },
//                description: {
//                    required: true
//                },
//                appropriate_time: {
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
        $('#donation-request-form .submit-form').click(function () {
            var validate_2 = $('#donation-request-form').validate().form();
            errorElements = errorElements1.concat(errorElements2);
            if (validate_2) {
                $('#donation-request-form .submit-form').prop('disabled', true);
                $('#donation-request-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#donation-request-form').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('#donation-request-form'));
            }

            return false;

        });
        $('#donation-request-form input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#donation-request-form').validate().form();
                errorElements = errorElements1.concat(errorElements2);
                if (validate_2) {
                    $('#donation-request-form .submit-form').prop('disabled', true);
                    $('#donation-request-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#donation-request-form').submit();
                    }, 1000);

                }
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('#donation-request-form'));
                }

                return false;
            }
        });
        $('#donation-request-form').submit(function () {
            var formData = new FormData($(this)[0]);
            var images = $('#images')[0].files;
            for (var x = 0; x < images.length; x++) {
                formData.append('images[]', images[x]);
            }
            $.ajax({
                url: config.url + "/donation-request",
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

                    $('#donation-request-form .submit-form').prop('disabled', false);
                    $('#donation-request-form .submit-form').html(lang.save);
                    if (data.type == 'success') {
//                        $('.alert-danger').hide();
//                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.customer_url + '/dashboard';
                        }, 3000);

                    } else {
                        $('#donation-request-form .submit-form').prop('disabled', false);
                        $('#donation-request-form .submit-form').html(lang.edit);
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
                    $('#donation-request-form .submit-form').prop('disabled', false);
                    $('#donation-request-form .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);

                },
            });

            return false;
        });

    }


    return {
        init: function () {
            init();
        },
        getLocation: function () {
            $('#getLocation').modal('show');
            $('#getLocation').on('shown.bs.modal', function () {
                Map.initMap(true, true, true, false);
                google.maps.event.trigger(map, 'resize');

            });
        }

    }

}();

jQuery(document).ready(function () {
    DonationRequest.init();
});


