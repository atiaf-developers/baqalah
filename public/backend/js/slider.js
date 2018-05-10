var Slider_grid;
var action = 'index';
var Slider = function () {
    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        My.readImageMulti('image');

    };

    var handleRecords = function () {

        Slider_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/slider/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "image"},
                {"data": "active"},
                {"data": "this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [1, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }
    var handleSubmit = function () {

        $('#addEditSliderForm').validate({
            ignore: "",
            rules: {
                title_ar: {
                    required: true

                },
                title_en: {
                    required: true

                },
                this_order: {
                    required: true,

                },
                image: {
                    required: true,
                    accept: "image/*",
                    filesize: 1000 * 1024
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
   
        $('#addEditSlider .submit-form').click(function () {
            if ($('#addEditSliderForm').validate().form()) {
                $('#addEditSlider .submit-form').prop('disabled', true);
                $('#addEditSlider .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditSliderForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditSliderForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditSliderForm').validate().form()) {
                    $('#addEditSlider .submit-form').prop('disabled', true);
                    $('#addEditSlider .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditSliderForm').submit();
                    }, 1000);

                }
                return false;
            }
        });



        $('#addEditSliderForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/slider';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/slider/' + id;
            }



            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    $('#addEditSlider .submit-form').prop('disabled', false);
                    $('#addEditSlider .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        Slider_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#addEditSlider').modal('hide');
                        } else {
                            Slider.empty();
                        }

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
                    $('#addEditSlider .submit-form').prop('disabled', false);
                    $('#addEditSlider .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }



    return{
        init: function () {
            init();
        },
        edit: function (t) {
            $('input[name="image"]').rules('remove', 'required');
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/slider/' + id,
                success: function (data)
                {
                    console.log(data);
                    Slider.empty();
                    My.setModalTitle('#addEditSlider', lang.edit_consultation_group);

                    for (i in data.message)
                    {
                        if (i == 'image') {
                            $('.image_box').html('<img style="height:80px;width:150px;" class="image plate_img"  src="' + config.public_path + '/uploads/slider/' + data.message[i] + '" alt="your image" />');
                        } else {
                            $('#' + i).val(data.message[i]);
                        }

                    }
                    $('#addEditSlider').modal('show');
                }
            });

        },
        delete: function (t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/slider/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {

                    Slider_grid.api().ajax.reload();


                }
            });
        },
        add: function () {
            action = 'add';
            Slider.empty();
            My.setModalTitle('#addEditSlider', lang.add_consultation_group);
            $('#addEditSlider').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            $('input[type="checkbox"]').prop("checked", false).trigger("change");
            $('.image_box').html('<img style="height:80px;width:150px;" class="image plate_img"  src="' + config.url + '/no-image.png' + '" alt="your image" />');
            My.emptyForm();
        },
    };
}();
$(document).ready(function () {
    Slider.init();
});