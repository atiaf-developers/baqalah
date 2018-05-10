var Address = function () {

    var init = function () {
        validateform();
    }

    var validateform = function ()
    {

        $("#addEditAddress").validate({
            ignore: "",
            rules: {
                city: {
                    required: true,
                },
                region: {
                    required: true,
                },
                sub_region: {
                    required: true,
                },
                street: {
                    required: true,
                },
                building_number: {
                    required: true,
                },
                floor_number: {
                    required: true,
                },
                apartment_number: {
                    required: true,
                },
            },

            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },

            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('');
            },
            errorPlacement: function (error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });

        $('#addEditAddress .submit-form').click(function () {

            if ($('#addEditAddress').validate().form()) {
                $('#addEditAddress .submit-form').prop('disabled', true);
                $('#addEditAddress .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditAddress').submit();
                }, 1000);
            }
            return false;
        });


        $('#addEditAddress').submit(function (e) {

            var form = $(this);
            e.preventDefault();
            var form_data = new FormData($(this)[0]);
            var method = "POST";
            var url = form.attr('action');

            var id = $('#id').val();
            if (id != 0) {
                form_data.append('_method', 'PATCH');
            }
            var return_url = App.getParameterByName('return', window.location.href);
            if (return_url !== null) {
                form_data.append('return', return_url);
            }

            $.ajax({
                type: method,
                url: url,
                dataType: 'json',
                data: form_data,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.type == 'success')
                    {
                        setTimeout(function () {
                            window.location.href = data.message;
                        }, 1000);

                    } else {
                        $('#addEditAddress .submit-form').prop('disabled', false);
                        $('#addEditAddress .submit-form').html(lang.send);
                        if (typeof data.errors === 'object') {
                            console.log(data.errors);
                            associate_errors(data.errors);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditAddress .submit-form').prop('disabled', false);
                    $('#addEditAddress .submit-form').html(lang.send);
                    App.ajax_error_message(xhr);
                },
            });
        });


    }

    var associate_errors = function (errors, form)
    {
        $('.help-block').html('');
        $.each(errors, function (index, value)
        {
            var element = 'input[name=' + index + ']';
            $(element).closest('.form-group').addClass('has-error');
            $(element).closest('.form-group').find('.help-block').html(value);


        }
        );
    }






    return{

        init: function () {
            init();
        },

    }


}();

$(document).ready(function () {
    Address.init();
});





