var Games_grid;

var Games = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        action = config.action;
        handleRecords();
        handleSubmit();
        handleChangeYoutubeUrl();
        My.readImageMulti('image_one');
        My.readImageMulti('image_two');
        My.readImageMulti('image_three');
    };

    var handleChangeYoutubeUrl = function () {
        $('#youtube_video_url').on('change', function () {
            var value = $(this).val();
            if (value && value != '') {
                var myId = My.getYoutubeEmbedUrl(value);

                $('#youtube_url').val(myId);

                $('#youtube-iframe').html('<iframe width="100%" height="315" src="//www.youtube.com/embed/' + myId + '" frameborder="0" allowfullscreen></iframe>');
            }

        })

    }
    var handleRecords = function () {
        Games_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/games/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "title", "name": "games_translations.title"},
                {"data": "active", "name": "games.active", searchable: false},
                {"data": "category_order", "name": "games.category_order"},
                {"data": "offers_order", "name": "games.offers_order"},
                {"data": "best_order", "name": "games.best_order"},
                {"data": "price", orderable: false, searchable: false},
                {"data": "image", orderable: false, searchable: false},
                {"data": "category", "name": "categories_translations.title", orderable: false},
                {"data": "created_at", searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [5, "desc"]
            ],

            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {


        $('#addEditGamesForm').validate({
            ignore: "",
            rules: {
                price: {
                    required: true,
                },
                youtube_url: {
                    required: true,
                },
                discount_price: {
                    required: true,
                },
                over_price: {
                    required: true,
                },
                active: {
                    required: true,
                },
                best_order: {
                    required: true,
                },
                category_order: {
                    required: true,
                },
                offers_order: {
                    required: true,
                },
                'gallery[0]': {
                    required: true,
                    accept: "image/*",
                    filesize: 1000 * 1024
                },
                'gallery[1]': {
                    required: true,
                    accept: "image/*",
                    filesize: 1000 * 1024
                },
                'gallery[2]': {
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
        var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
            var title = "input[name='title[" + langs[x] + "]']";
            var description = "textarea[name='description[" + langs[x] + "]']";
            $(title).rules('add', {
                required: true
            });
            $(description).rules('add', {
                required: true
            });
        }

        if (action == 'edit') {
            $('input[name="gallery[0]"]').rules('remove', 'required');
            $('input[name="gallery[1]"]').rules('remove', 'required');
            $('input[name="gallery[2]"]').rules('remove', 'required');
        }




        $('#addEditGamesForm .submit-form').click(function () {

            if ($('#addEditGamesForm').validate().form()) {
                $('#addEditGamesForm .submit-form').prop('disabled', true);
                $('#addEditGamesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditGamesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditGamesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditGamesForm').validate().form()) {
                    $('#addEditGamesForm .submit-form').prop('disabled', true);
                    $('#addEditGamesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditGamesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditGamesForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/games';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/games/' + id;
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
                    $('#addEditGamesForm .submit-form').prop('disabled', false);
                    $('#addEditGamesForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        if (id == 0) {
                            Games.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                var message = data.errors[i];
                                if (i.startsWith('title') || i.startsWith('description')) {
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
                    $('#addEditGamesForm .submit-form').prop('disabled', false);
                    $('#addEditGamesForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/games/' + id,
                success: function (data)
                {
                    console.log(data);

                    Games.empty();
                    My.setModalTitle('#addEditGames', lang.edit);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditGames').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/games/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Games_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            Games.empty();
            My.setModalTitle('#addEditGames', lang.add);
            $('#addEditGames').modal('show');
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
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Games.init();
});

