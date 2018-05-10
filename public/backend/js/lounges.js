var Lounges_grid;

var Lounges = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
    
        handleRecords();
  
        handleSubmit();


    };
  




    var handleRecords = function () {
        Lounges_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/lounges/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "number"},
                {"data": "this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [1, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditLoungesForm').validate({
            rules: {
                suite_number: {
                    required: true,
                },
                this_order: {
                    required: true,
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



        $('#addEditLounges .submit-form').click(function () {

            if ($('#addEditLoungesForm').validate().form()) {
                $('#addEditLounges .submit-form').prop('disabled', true);
                $('#addEditLounges .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditLoungesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditLoungesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditLoungesForm').validate().form()) {
                    $('#addEditLounges .submit-form').prop('disabled', true);
                    $('#addEditLounges .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditLoungesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditLoungesForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/lounges';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/lounges/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditLounges .submit-form').prop('disabled', false);
                    $('#addEditLounges .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        My.toast(data.message);
                        Lounges_grid.api().ajax.reload();
                        if (id == 0) {
                            Lounges.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors) {
                                var message = data.errors[i];
              
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]')
                                        .closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditLounges .submit-form').prop('disabled', false);
                    $('#addEditLounges .submit-form').html(lang.save);
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
        edit: function (t) {
    
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/lounges/' + id,
                success: function (data) {
                    console.log(data);

                    Lounges.empty();
                    My.setModalTitle('#addEditLounges', lang.edit);

                    for (i in data.message) {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditLounges').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/lounges/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    Lounges_grid.api().ajax.reload();


                }
            });

        },
        delete_lounge: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/lounges/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    Lounges_grid.api().ajax.reload();


                }
            });

        },
        add: function () {
            Lounges.empty();
            My.setModalTitle('#addEditLounges', lang.add);
            $('#addEditLounges').modal('show');
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
                        action: function () {}
                    }
                }
            });
        },
        empty: function () {
            $('#id').val(0);
            $('#category_icon').val('');
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.image_uploaded_box').html('<img src="' + config.base_url + 'no-image.png" class="category_icon" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Lounges.init();
});