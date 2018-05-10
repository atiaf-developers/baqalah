var Activities_grid;

var Activities = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        My.readImageMulti('image_one');
        My.readImageMulti('image_two');
        My.readImageMulti('image_three');
        My.readImageMulti('image_four');
        My.readImageMulti('image_five');
    };


    var handleRecords = function () {
        Activities_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/activities/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title","name":"activities_translations.title"},
                {"data": "active","name":"activities.active", orderable: false, searchable: false},
                {"data": "this_order","name":"activities.this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditActivitiesForm').validate({
            rules: {
                 this_order: {
                     required: true,
                 },
                 active:{
                     required : true,
                 },

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
        
        
       var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
             var ele = "textarea[name='description[" + langs[x] + "]']";
             var ele2 = "input[name='title[" + langs[x] + "]']";
             $(ele).rules('add', {
                 required: true
             });
             $(ele2).rules('add', {
                 required: true
             });
         }

        $('#addEditActivitiesForm .submit-form').click(function () {

            if ($('#addEditActivitiesForm').validate().form()) {
                $('#addEditActivitiesForm .submit-form').prop('disabled', true);
                $('#addEditActivitiesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditActivitiesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditActivitiesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditActivitiesForm').validate().form()) {
                    $('#addEditActivitiesForm .submit-form').prop('disabled', true);
                    $('#addEditActivitiesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditActivitiesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditActivitiesForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/activities';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/activities/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditActivitiesForm .submit-form').prop('disabled', false);
                    $('#addEditActivitiesForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        if (id == 0) {
                            Activities.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                var message=data.errors[i];
                                 if (i.startsWith('title') || i.startsWith('description')|| i.startsWith('images')) {
                                    var key_arr = i.split('.');
                                    var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                    i = key_text;
                                }
                                 
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1);
                            }
                        } 
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditActivitiesForm .submit-form').prop('disabled', false);
                    $('#addEditActivitiesForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }

    return {
        init: function () {
            init();
        },
       
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/activities/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Activities_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            Activities.empty();
            if (parent_id > 0) {
                $('.for-country').hide();
                $('.for-city').show();
            } else {
                $('.for-country').show();
                $('.for-city').hide();
            }

            My.setModalTitle('#addEditActivitiesForm', lang.add_location);
            $('#addEditActivitiesForm').modal('show');
        },

        error_message: function (message) {
            $.alert({
                title: lang.error,
                content: message,
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
        },
        empty: function () {
            $('#id').val(0);
            $('#category_icon').val('');
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.image_one_box').html('<img src="' + config.url + '/no-image.png" class="image_one" width="150" height="80" />');
            $('.image_two_box').html('<img src="' + config.url + '/no-image.png" class="image_two" width="150" height="80" />');
            $('.image_three_box').html('<img src="' + config.url + '/no-image.png" class="image_three" width="150" height="80" />');
            $('.image_four_box').html('<img src="' + config.url + '/no-image.png" class="image_four" width="150" height="80" />');
            $('.image_five_box').html('<img src="' + config.url + '/no-image.png" class="image_five" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Activities.init();
});

