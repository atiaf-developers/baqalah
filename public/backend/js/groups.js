var Groups_grid;
var Groups = function () {
    var init = function () {
        //alert('heree');
        $.extend(lang, new_lang);
        //console.log(lang);
        handleRecords();
        handleSubmit();

    };

    var handleRecords = function () {

        Groups_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/groups/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "name"},
                {"data": "active"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [1, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }
    var handleSubmit = function () {

        $('#addEditGroupsForm').validate({
            rules: {
                name: {
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
        $('#addEditGroups .submit-form').click(function () {
            if ($('#addEditGroupsForm').validate().form()) {
                $('#addEditGroups .submit-form').prop('disabled', true);
                $('#addEditGroups .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditGroupsForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditGroupsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditGroupsForm').validate().form()) {
                    $('#addEditGroups .submit-form').prop('disabled', true);
                    $('#addEditGroups .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditGroupsForm').submit();
                    }, 1000);

                }
                return false;
            }
        });



        $('#addEditGroupsForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/groups';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/groups/' + id;
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
                    $('#addEditGroups .submit-form').prop('disabled', false);
                    $('#addEditGroups .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        Groups_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#addEditGroups').modal('hide');
                        } else {
                            Groups.empty();
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
                    $('#addEditGroups .submit-form').prop('disabled', false);
                    $('#addEditGroups .submit-form').html(lang.save);
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
                url: config.admin_url + '/groups/' + id,
                success: function (data)
                {
                    console.log(data);
                    Groups.empty();
                    My.setModalTitle('#addEditGroups', lang.edit_group);

                    $('#id').val(data.message['id']);
                    $('#name').val(data.message['name']);
                    $('#active').val(data.message['active']);
                    var permissions = data.message['permissions'];
                    for (i in permissions)
                    {
                        var page_name = i;
                        var page_permissions = permissions[i];
                        for (x in page_permissions)
                        {
                            $('#' + page_name + '_' + x).prop("checked", true).trigger("change");

                        }

                    }
                    $('#addEditGroups').modal('show');
                }
            });

        },
        delete: function (t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/groups/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {

                    Groups_grid.api().ajax.reload();


                }
            });
        },
        add: function () {
            Groups.empty();
            My.setModalTitle('#addEditGroups', lang.add_group);
            $('#addEditGroups').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            $('input[type="checkbox"]').prop("checked", false).trigger("change");
            My.emptyForm();
        },
    };
}();
$(document).ready(function () {
    Groups.init();
});