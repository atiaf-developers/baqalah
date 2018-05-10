var currentTab = 0;
var activation_code = false;
var DonationRequest = function () {

    var init = function () {
        handle_register();
        showTab(currentTab);
        $(document).ready(function () {
            $('#appropriate_time').dateTimePicker();
        });
        Map.initMap(true, true, true, false);

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
//            if (currentTab == 0) {
//                var images = $('#images')[0].files;
//                console.log(images.length);
//                for (var x = 0; x < images.length; x++) {
//                    formData.append('images[]', images[x]);
//                }
//            }

            if (activation_code) {
                formData.append('ajax_code', activation_code);
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


                    if (data.type == 'success') {
                        $('#nextBtn').prop('disabled', false);
                        $('#nextBtn').html(lang.next);
                        var step = data.data.step;
                        if (!config.isUser) {
                            if (step == 3) {
                                $('.next2').hide();
                                $('.alert-danger').hide();
                                $('.alert-success').show().find('.message').html(data.data.message);
                            } else if (step == 2) {
                                $('#mobile-message').html($('#mobile').val());
                                activation_code = data.data.activation_code;

                            }
                        } else {
                            if (step == 2) {
                                $('.next2').hide();
                                $('.alert-danger').hide();
                                $('.alert-success').show().find('.message').html(data.data.message);
                            }
                        }

                        var hideTab = currentTab;
                        currentTab = currentTab + 1;
                        $('.tab:eq(' + hideTab + ')').hide();
                        showTab(currentTab);

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
    DonationRequest.init();
});


