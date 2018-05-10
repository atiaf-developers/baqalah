var Settings = function() {

    var init = function() {
        //$.extend(lang, new_lang);
        handleSubmit();
        My.readImageMulti('about_image');
        Map.initMap(true, true, true, false);
    };

    var handleSubmit = function() {

        $('#editSettingsForm').validate({
            ignore: "",
            rules: {
                'setting[phone]': {
                    required: true
                },
                'setting[email]': {
                    required: true,
                    email: true
                },
                'setting[social_media][facebook]': {
                    required: true
                },
                'setting[social_media][twitter]': {
                    required: true
                },
                'setting[social_media][instagram]': {
                    required: true
                },
                'setting[social_media][google]': {
                    required: true
                },
                'setting[social_media][youtube]': {
                    required: true
                },
                'setting[store][android]': {
                    required: true
                },
                'setting[store][ios]': {
                    required: true
                },
                about_image: {
                    accept: "image/*",
                    filesize: 1000 * 1024
                },

            },
            messages: lang.messages,
            highlight: function(element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);
            },
            errorPlacement: function(error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });
        var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
            // var title = "input[name='title[" + langs[x] + "]']";
            var description = "textarea[name='description[" + langs[x] + "]']";
            var address = "textarea[name='address[" + langs[x] + "]']";
            var about = "textarea[name='about[" + langs[x] + "]']";
            var policy = "textarea[name='policy[" + langs[x] + "]']";
            // $(title).rules('add', {
            //     required: true
            // });
            $(description).rules('add', {
                required: true
            });
            $(address).rules('add', {
                required: true
            });
            $(about).rules('add', {
                required: true
            });
            $(policy).rules('add', {
                required: true
            });
        }
        $('#editSettingsForm .submit-form').click(function() {
            if ($('#editSettingsForm').validate().form()) {
                $('#editSettingsForm .submit-form').prop('disabled', true);
                $('#editSettingsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function() {
                    $('#editSettingsForm').submit();
                }, 500);
            }
            return false;
        });
        $('#editSettingsForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#editSettingsForm').validate().form()) {
                    $('#editSettingsForm .submit-form').prop('disabled', true);
                    $('#editSettingsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function() {
                        $('#editSettingsForm').submit();
                    }, 500);
                }
                return false;
            }
        });



        $('#editSettingsForm').submit(function() {
            var id = $('#id').val();
            var action = config.admin_url + '/settings';
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#editSettingsForm .submit-form').prop('disabled', false);
                    $('#editSettingsForm .submit-form').html(lang.save);

                    if (data.type == 'success') {

                        toastr.options = {
                            "debug": false,
                            "positionClass": "toast-bottom-left",
                            "onclick": null,
                            "fadeIn": 300,
                            "fadeOut": 1000,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000
                        };
                        toastr.success(lang.updated_successfully, 'رسالة');

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors) {
                                var message = data.errors[i];
                                if (i.startsWith('title') || i.startsWith('description') || i.startsWith('address') || i.startsWith('about')) {
                                    var key_arr = i.split('.');
                                    var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                    i = key_text;
                                }
                                $('[name="' + i + '"]')
                                    .closest('.form-group').addClass('has-error');
                                $('#' + i).closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $.confirm({
                                title: lang.error,
                                content: data.message,
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: lang.try_again,
                                        btnClass: 'btn-red',
                                        action: function() {}
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#editSettingsForm .submit-form').prop('disabled', false);
                    $('#editSettingsForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }

    return {
        init: function() {
            init();
        }
    };

}();
jQuery(document).ready(function() {
    Settings.init();
});