var DonationRequests_grid;
var count;
var DonationRequests = function () {

    var init = function () {
        $.extend(lang, new_lang);
        handleRecords();
        if ($('#map').length > 0) {
            Map.initMap(false, false, false);
        }
        handleAssignedForm();
        handleStatusForm();
        handleInvoiceEditForm();
        handleAddOrRemoveItem();

        handleReport();

    };
    var handleReport = function () {
        $('.btn-report').on('click', function () {
            var data = $("#orders-reports").serializeArray();


            var url = config.admin_url + "/donation_requests";
            var params = {};
            $.each(data, function (i, field) {
                var name = field.name;
                var value = field.value;
                if (value) {
                    if (name == "from" || name == "to") {
                        value = new Date(Date.parse(value));
                        value = getDate(value);
                    }
                    params[field.name] = field.value
                }

            });
            var query = $.param(params);
            url += '?' + query;

            window.location.href = url;
            return false;
        })
    }

    var getDate = function (date) {
        var dd = date.getDate();
        var mm = date.getMonth() + 1; //January is 0!
        var yyyy = date.getFullYear();
        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        var edited_date = yyyy + '-' + mm + '-' + dd;
        return edited_date;
    }

    var handleAddOrRemoveItem = function () {
        $(document).on('click', '.remove-item', function () {
            $(this).closest('tr').remove();
            count--;
        })
        $('.add-item').on('click', function () {
            var html = '<tr class="material-one">' +
                    '<td><input type="text" class="form-control form-filter input-sm" name="price_list[material][' + count + '][text]" value=""></td>' +
                    '<td><input type="text" class="form-control form-filter input-sm" name="price_list[material][' + count + '][price]" value=""></td>' +
                    '<td><a class="btn btn-danger remove-item">' + lang.remove + '</a></td>' +
                    '</tr>';
            $('#material-table tbody').append(html);
            count++;

        })
    }

    var handleInvoiceEditForm = function () {

        $('#invoiceEditForm .submit-form').click(function () {

            if ($('#invoiceEditForm').validate().form()) {
                $('#invoiceEditForm .submit-form').prop('disabled', true);
                $('#invoiceEditForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#invoiceEditForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#invoiceEditForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#invoiceEditForm').validate().form()) {
                    $('#invoiceEditForm .submit-form').prop('disabled', true);
                    $('#invoiceEditForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#invoiceEditForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#invoiceEditForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/orders/invoice';
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#invoiceEditForm .submit-form').prop('disabled', false);
                    $('#invoiceEditForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        window.location.href = data.message;


                    } else {
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('#' + i).parent().find(".help-block").html(data.errors[i]).css('opacity', 1)
                            }
                        } else {
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
                    $('#invoiceEditForm .submit-form').prop('disabled', false);
                    $('#invoiceEditForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }
    var handleAssignedForm = function () {
        $('#assignedForm').validate({
            rules: {
                delegate: {
                    required: true,
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
        $('#assignedForm .submit-form').click(function () {

            if ($('#assignedForm').validate().form()) {
                $('#assignedForm .submit-form').prop('disabled', true);
                $('#assignedForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#assignedForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#assignedForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#assignedForm').validate().form()) {
                    $('#assignedForm .submit-form').prop('disabled', true);
                    $('#assignedForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#assignedForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#assignedForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/donation_requests/assigned';
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#assignedForm .submit-form').prop('disabled', false);
                    $('#assignedForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        window.location.href = data.message;


                    } else {
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('#' + i).parent().find(".help-block").html(data.errors[i]).css('opacity', 1)
                            }
                        } else {
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
                    $('#assignedForm .submit-form').prop('disabled', false);
                    $('#assignedForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }
    var handleStatusForm = function () {
        $('#statusForm').validate({
            rules: {
                status: {
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
        $('#statusForm .submit-form').click(function () {

            if ($('#statusForm').validate().form()) {
                $('#statusForm .submit-form').prop('disabled', true);
                $('#statusForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#statusForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#statusForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#statusForm').validate().form()) {
                    $('#statusForm .submit-form').prop('disabled', true);
                    $('#statusForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#statusForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#statusForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/orders/status';
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#statusForm .submit-form').prop('disabled', false);
                    $('#statusForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        window.location.href = data.message;


                    } else {
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('#' + i).parent().find(".help-block").html(data.errors[i]).css('opacity', 1)
                            }
                        } else {
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
                    $('#statusForm .submit-form').prop('disabled', false);
                    $('#statusForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }
    var handleRecords = function () {
        DonationRequests_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/orders/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "id"},
                {"data": "date"},
                {"data": "time"},
                {"data": "status"},
                {"data": "created_at"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [4, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
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

                    DonationRequests.empty();
                    My.setModalTitle('#addEditDonationRequests', lang.edit_country);

                    for (i in data.message)
                    {
                        if (i == 'image') {
                            $('.image_uploaded_box').html('<img style="height:80px;width:150px;" class="image" id="image_upload_preview" src="' + config.public_path + '/uploads/DonationRequests/' + data.message[i] + '" alt="your image" />');
                        } else {
                            $('#' + i).val(data.message[i]);
                        }

                    }
                    $('#addEditDonationRequests').modal('show');
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
                    DonationRequests_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            DonationRequests.empty();
            My.setModalTitle('#addEditDonationRequests', lang.add_country);
            $('#addEditDonationRequests').modal('show');
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
            $('.image_uploaded_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        },
        print: function (div)
        {
            var mywindow = window.open('', 'PRINT', 'height=600,width=800');

            mywindow.document.write('<html><head><title>' + document.title + '</title>');
            mywindow.document.write('</head><body >');
            mywindow.document.write('<h1>' + document.title + '</h1>');

            mywindow.document.write(document.getElementById(div).innerHTML);
            mywindow.document.write('</body></html>');

//            mywindow.document.close(); // necessary for IE >= 10
//            mywindow.focus(); // necessary for IE >= 10*/

            mywindow.print();
            //mywindow.close();

            return false;
        }
    };

}();
jQuery(document).ready(function () {
    DonationRequests.init();
});

