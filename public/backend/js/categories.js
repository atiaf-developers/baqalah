var Categories_grid;
var parent_id = 0;

var Categories = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        parent_id = config.parent_id;
        
        handleRecords();
        handleSubmit();
        My.readImageMulti('image');
    };

    var handleRecords = function () {

        Categories_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/categories/data",
                "type": "POST",
                data: {parent_id:parent_id,_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title","name":"categories_translations.title"},
                {"data": "active","name":"categories.active",searchable: false},
                {"data": "this_order","name":"categories.this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
             "order": [
                [2, "asc"]
            ],

            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {


        $('#addEditCategoriesForm').validate({
            rules: {
                active: {
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
        var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
            var title = "input[name='title[" + langs[x] + "]']";

            $(title).rules('add', {
                required: true
            });
        }

        var $description_div = $('#description');

        if ( $description_div.length){

            for (var x = 0; x < langs.length; x++) {
                var description = "textarea[name='description[" + langs[x] + "]']";
                $(description).rules('add', {
                    required: true
                });
            }
        }

        
       

        $('#addEditCategoriesForm .submit-form').click(function () {

            if ($('#addEditCategoriesForm').validate().form()) {
                $('#addEditCategoriesForm .submit-form').prop('disabled', true);
                $('#addEditCategoriesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditCategoriesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditCategoriesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditCategoriesForm').validate().form()) {
                    $('#addEditCategoriesForm .submit-form').prop('disabled', true);
                    $('#addEditCategoriesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditCategoriesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditCategoriesForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/categories';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/categories/' + id;
            }
             formData.append('parent_id',parent_id);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    $('#addEditCategoriesForm .submit-form').prop('disabled', false);
                    $('#addEditCategoriesForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        if (id == 0) {
                            Categories.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                 var message=data.errors[i];
                                 if (i.startsWith('title')) {
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
                    $('#addEditCategoriesForm .submit-form').prop('disabled', false);
                    $('#addEditCategoriesForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/categories/' + id,
                success: function (data)
                {
                    console.log(data);

                    Categories.empty();
                    My.setModalTitle('#addEditCategories', lang.edit);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditCategories').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/categories/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Categories_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            Categories.empty();
            My.setModalTitle('#addEditCategories', lang.add);
            $('#addEditCategories').modal('show');
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
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.image_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Categories.init();
});

