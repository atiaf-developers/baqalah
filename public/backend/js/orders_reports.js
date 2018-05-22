
var Orders = function () {

    var init = function () {

        handleReport();
        handleSubmit();
        handleChangeDeliveryType();
        if ($('#map').length > 0) {
            Map.initMap(false, false, false);
        }

    };

    var handleChangeDeliveryType = function () {
        $('#delivery_type').on('change', function () {
            var value = $(this).val();
            if (value == 1) {
                $('.status-one-box').show();
                $('.status-two-box').hide();
            } else {
                $('.status-one-box').hide();
                $('.status-two-box').show();
            }
        });
    }
    var handleSubmit = function () {

        $('#orderStatusForm').validate({
            rules: {

                reply: {
                    required: true
                }

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
        $('#orderStatusForm .submit-form').click(function () {
            if ($('#orderStatusForm').validate().form()) {
                $('#orderStatusForm .submit-form').prop('disabled', true);
                $('#orderStatusForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#orderStatusForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#orderStatusForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#orderStatusForm').validate().form()) {
                    $('#orderStatusForm .submit-form').prop('disabled', true);
                    $('#orderStatusForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#orderStatusForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#orderStatusForm').submit(function () {
            var formData = new FormData($(this)[0]);
            var action = config.admin_url + '/orders_reports/reply';


            $.ajax({
                url: action,
                type: 'post',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#orderStatusForm .submit-form').prop('disabled', false);
                    $('#orderStatusForm .submit-form').html(lang.save);

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
                        location.reload();
                        // console.log(data);
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
                    $('#orderStatusForm .submit-form').prop('disabled', false);
                    $('#orderStatusForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }

    var handleReport = function () {
        $('.btn-report').on('click', function () {
            var data = $("#orders-reports").serializeArray();


            var url = config.admin_url + "/orders_reports";
            var params = {};
            $.each(data, function (i, field) {
                var name = field.name;
                var value = field.value;
                if (value) {
                    if (name == "from" || name == "to") {
                        value = new Date(Date.parse(value));
                        value = getDate(value);
                    }
                    if (name == "status_one" || name == "status_two") {
              
                        name = "status";
                    }

                    params[name] = value
                }

            });
            query = $.param(params);
            url += '?' + query;

            window.location.href = url;
            return false;
        })
    }

    var getDate = function (date) {
        var dd = date.getDate();
        var mm = date.getMonth() + 1; //January is 0!
        var yyyy = date.getFullYear();
        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        var edited_date = yyyy + '-' + mm + '-' + dd;
        return edited_date;
    }





    return {
        init: function () {
            init();
        },

    }

}();
jQuery(document).ready(function () {
    Orders.init();
});

