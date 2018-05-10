var AlbumImages_grid;
var album_id;
var AlbumImages = function () {
    var init = function () {
        //alert('heree');
        $.extend(lang, new_lang);
         $.extend(config, new_config);
        album_id = config.album_id;
        //console.log(lang);
        handleRecords();
        handleSubmit();
       
        My.readImageMulti('image');

    };
    
    var handleRecords = function () {

        AlbumImages_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/album_images/data",
                "type": "POST",
                data: {album_id: album_id,_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "image", orderable: false, searchable: false},
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

        $('#addEditAlbumImagesForm').validate({
            rules: {
                this_order: {
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
        $('#addEditAlbumImages .submit-form').click(function () {
            if ($('#addEditAlbumImagesForm').validate().form()) {
                $('#addEditAlbumImages .submit-form').prop('disabled', true);
                $('#addEditAlbumImages .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditAlbumImagesForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditAlbumImagesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditAlbumImagesForm').validate().form()) {
                    $('#addEditAlbumImages .submit-form').prop('disabled', true);
                    $('#addEditAlbumImages .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditAlbumImagesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditAlbumImagesForm').submit(function () {
            var id = $('#id').val();
            var formData = new FormData($(this)[0]);
            var action = config.admin_url + '/album_images';
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/album_images/' + id;
            }


            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditAlbumImages .submit-form').prop('disabled', false);
                    $('#addEditAlbumImages .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        AlbumImages_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#addEditAlbumImages').modal('hide');
                        } else {
                            AlbumImages.empty();
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
                    $('#addEditAlbumImages .submit-form').prop('disabled', false);
                    $('#addEditAlbumImages .submit-form').html(lang.save);
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
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/album_images/' + id,
                success: function (data)
                {
                    
                    AlbumImages.empty();
                    My.setModalTitle('#addEditAlbumImages', lang.edit_admin);

                    for (i in data.message)
                    {
                        if (i == 'image') {
                            $('.image_box').html('<img style="height:80px;width:150px;" class="image"  src="' + config.public_path + '/uploads/albums/' + data.message[i] + '" alt="your image" />');
                        } else {
                            $('#' + i).val(data.message[i]);
                        }
                        
                       
                    }
                    $('#addEditAlbumImages').modal('show');
                }
            });

        },
        delete: function (t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/album_images/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {

                    AlbumImages_grid.api().ajax.reload();


                }
            });
        },
        add: function () {
            AlbumImages.empty();
            My.setModalTitle('#addEditAlbumImages', lang.add_admin);
            $('#addEditAlbumImages').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            $('#image').val('');
            $('.image_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');
            $('#active').find('option').eq(0).prop('selected', true);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        },
    };
}();
$(document).ready(function () {
    AlbumImages.init();
});