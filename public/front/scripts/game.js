var Game = function () {

    var init = function () {
        Map.initMap(true, true, true, false);

        handle_reserve();
        handleChangeDate();
    }
    var handleChangeDate = function () {
        $('#reservation_date').on('change', function () {
            var value = $(this).val();
            if (value && value != '') {
                $.ajax({
                    url: config.url + "/ajax/checkAvailability",
                    type: 'GET',
                    dataType: 'json',
                    data: {date: value,game_id:$('#game_id').val()},
                    async: false,
                    success: function (data)
                    {
                        console.log(data);
                        if (data.type == 'success') {

                            var html = '<option value="">' + lang.choose + '</option>';
                            for (var x in data.message) {
                                var item = data.message[x];
                                html += '<option value="' + item.from + '">' + item.from + "-" + item.to + '</option>';
                            }
                            $('#reservation_time').html(html);

                        }



                    },
                    error: function (xhr, textStatus, errorThrown) {

                        App.ajax_error_message(xhr);

                    },
                });
            }

        });
    }
    var handle_reserve = function () {
        $("#reserve-form").validate({
            ignore: "",
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                phone: {
                    required: true
                },
                lat: {
                    required: true
                },
                lng: {
                    required: true
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
                errorElements1.push(element);
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });
        $('#reserve-form .submit-form').click(function () {
            var validate_2 = $('#reserve-form').validate().form();
            errorElements = errorElements1.concat(errorElements2);
            if (validate_2) {
                $('#reserve-form .submit-form').prop('disabled', true);
                $('#reserve-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#reserve-form').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('#reserve-form'));
            }

            return false;

        });
        $('#reserve-form input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#reserve-form').validate().form();
                errorElements = errorElements1.concat(errorElements2);
                if (validate_2) {
                    $('#reserve-form .submit-form').prop('disabled', true);
                    $('#reserve-form .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#reserve-form').submit();
                    }, 1000);

                }
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('#reserve-form'));
                }

                return false;
            }
        });
        $('#reserve-form').submit(function () {
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: config.url + "/ajax/reserve_submit",
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

                    $('#reserve-form .submit-form').prop('disabled', false);
                    $('#reserve-form .submit-form').html(lang.reserve_now);
                    if (data.type == 'success') {
                        $('.alert-danger').hide();
                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.customer_url + '/reservations';
                        }, 3000);

                    } else {
                        $('#reserve-form .submit-form').prop('disabled', false);
                        $('#reserve-form .submit-form').html(lang.reserve);
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
                    $('#reserve-form .submit-form').prop('disabled', false);
                    $('#reserve-form .submit-form').html(lang.reserve);
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
        ,

    }


}();

$(document).ready(function () {
    Game.init();
});