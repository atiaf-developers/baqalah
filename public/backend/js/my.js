var My = function () {

    // IE mode
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;

    var resizeHandlers = [];

    var assetsPath = 'assets/';

    var globalImgPath = 'global/img/';

    var globalPluginsPath = 'global/plugins/';

    var globalCssPath = 'global/css/';
    var ajaxGoToXHR;
    // theme layout color set

    var brandColors = {
        'blue': '#89C4F4',
        'red': '#F3565D',
        'green': '#1bbc9b',
        'purple': '#9b59b6',
        'grey': '#95a5a6',
        'yellow': '#F8CB00'
    };
    var handleNewValidatorMethods = function () {
        $.validator.addMethod('filesize', function (value, element, param) {
            if (element.files.length > 0) {
                return this.optional(element) || (element.files[0].size <= param)
            }
            return true;


        }, function (params, element) {
            var message = lang.filesize_can_not_be_more_than
            return message + ' ' + params;
        });
    }


    return {
        //main function to initiate the theme
        init: function () {
            handleNewValidatorMethods();
        },
        toast: function (message) {
            toastr.options = {
                "debug": false,
                "positionClass": "toast-bottom-left",
                "onclick": null,
                "fadeIn": 300,
                "fadeOut": 1000,
                "timeOut": 5000,
                "extendedTimeOut": 1000,
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.success(message, lang.message);
        },
        getYoutubeEmbedUrl: function (url) {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            var match = url.match(regExp);

            if (match && match[2].length == 11) {
                return match[2];
            } else {
                return 'error';
            }
        },
        readImage: function (input) {

            $(document).on('click', "." + input, function () {
                $("#" + input).trigger('click');
            });
            $(document).on('change', "#" + input, function () {
                //alert($(this)[0].files.length);
                for (var i = 0; i < $(this)[0].files.length; i++) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('.image_uploaded_box').html('<img style="height:80px;width:150px;" id="image_upload_preview" class="' + input + '" src="' + e.target.result + '" alt="your image" />');
                    }

                    reader.readAsDataURL($(this)[0].files[i]);
                }

            });



        },
        readImageMulti: function (input) {

            $(document).on('click', "." + input, function () {
                $("#" + input).trigger('click');
            });
            $(document).on('change', "#" + input, function () {
                //alert($(this)[0].files.length);
                for (var i = 0; i < $(this)[0].files.length; i++) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('.' + input + '_box').html('<img style="height:80px;width:150px;" id="image_upload_preview" class="' + input + '" src="' + e.target.result + '" alt="your image" />');
                    }

                    reader.readAsDataURL($(this)[0].files[i]);
                }

            });



        },
        number_format: function (number, decimals, dec_point, thousands_sep) {


            number = (number + '')
                    .replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    s = '',
                    toFixedFix = function (n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + (Math.round(n * k) / k)
                                .toFixed(prec);
                    };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
                    .split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '')
                    .length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1)
                        .join('0');
            }
            return s.join(dec);
        },

        ajax_error_message: function (xhr) {
            var message;
            if (xhr.status == 403) {
                message = 'The action you have requested is not allowed';
            } else {
                message = xhr.responseText;
                if (typeof xhr.responseJSON !== "undefined")
                {
                    message = xhr.responseJSON.message;
                }

            }
            bootbox.dialog({
                message: message,
                title: lang.error,
                buttons: {
                    danger: {
                        label: lang.close,
                        className: "red"
                    }
                }
            });

        }
        ,
        set_error: function (id, msg) {
            $('[name="' + id + '"]')
                    .closest('.form-group').addClass('has-error').removeClass("has-info");
            $('#' + id).parent()

            if ($("#" + id).parent().hasClass("input-group"))
            {
                $help_block = $('#' + id).parent().parent().find('.help-block');
            } else {
                $help_block = $('#' + id).parent().find('.help-block');
            }


            if ($help_block.length)
            {
                $help_block.html(msg);
            } else {
                if ($("#" + id).parent().hasClass("input-group"))
                    $('#' + id).parent().parent().append('<span class="help-block">' + msg + '</span>');
                else
                    $('#' + id).parent().append('<span class="help-block">' + msg + '</span>');
            }
        }
        ,
        set_errors: function (errors) {
            for (var i in errors)
            {
                My.set_error(i, errors[i]);
            }
        }
        ,
        initCheckbox: function () {

            if ($('#checkAll').length == 0)
                return false;

            var checkboxes = document.querySelectorAll('input.check-me'),
                    checkall = document.getElementById('checkAll');

            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].onclick = function () {
                    var checkedCount = document.querySelectorAll('input.check-me:checked').length;

                    checkall.checked = checkedCount > 0;
                    checkall.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
                    if (checkedCount > 0)
                    {
                        $('#delete-selected').prop("disabled", false);
                    } else {
                        $('#delete-selected').prop("disabled", true);
                    }
                    if (checkedCount > 0 && checkedCount < checkboxes.length)
                    {
                        $('#checkAll').parent().addClass("indeterminate").removeClass("checked");
                    } else {
                        $('#checkAll').parent().removeClass("indeterminate");
                    }
                    $('#delete-num').html(checkedCount)
                }
            }

            checkall.onclick = function () {

                var checkedCount = document.querySelectorAll('input.check-me:checked').length;
                if (checkedCount > 0 && checkedCount < checkboxes.length)
                {
                    this.checked = true;
                } else if (checkedCount == 0) {
                    this.checked = true;
                } else {
                    this.checked = false;
                }

                $('#checkAll').parent().addClass("checked").removeClass("indeterminate");

                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }

                if (document.querySelectorAll('input.check-me:checked').length > 0)
                {
                    $('#delete-selected').prop("disabled", false);
                } else {
                    $('#delete-selected').prop("disabled", true);
                }

                $('#delete-num').html(document.querySelectorAll('input.check-me:checked').length)
            }
        }
        ,
        emptyForm: function () {
            // $('[data-role="tagsinput"]').tagsinput('removeAll');
            $('input[type="text"],input[type="email"],input[type="date"],input[type="password"],input[type="number"],textarea').val("");
            //$('input[type="checkbox"]').prop("checked", false).trigger("change");
        }
        ,
        scrollTo: function (el, offeset) {
            var pos = (el && el.size() > 0) ? el.offset().top : 0;

            if (el) {
                if ($('body').hasClass('page-header-fixed')) {
                    pos = pos - $('.page-header').height();
                } else if ($('body').hasClass('page-header-top-fixed')) {
                    pos = pos - $('.page-header-top').height();
                } else if ($('body').hasClass('page-header-menu-fixed')) {
                    pos = pos - $('.page-header-menu').height();
                }
                pos = pos + (offeset ? offeset : -1 * el.height());
            }

            $('html,body').animate({
                scrollTop: pos
            }, 'slow');
        }
        ,
        setModalTitle: function (id, title)
        {
            $(id + 'Label').html(title);
        }
        ,
        clearToolTip: function () {
            $('.tooltip.fade').each(function () {
                if ($(this).attr("id"))
                {
                    var $id = $(this).attr("id");
                    $('[aria-describedby="' + $id + '"]').removeAttr("aria-describedby");
                    $(this).remove()
                }
            })
        }
        ,
        editForm: function (args) {

            //My.clearToolTip();


            $.ajax({
                url: args.url,
                data: args.data,
                type: "GET",
                success: function (data)
                {


                    args.success(data);
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('.loading').addClass('hide');
                    My.ajax_error_message(xhr);

                },
                dataType: "json",
            })
            return false;

        }
        ,
        clearFormErrors: function () {
            $('.has-error').removeClass("has-error");
            $('.help-block').html("");
        }
        ,
        Ajax: function (args) {

            My.clearToolTip();

            $.ajax({
                url: config.site_url + args.url,
                data: args.data,
                success: function (data)
                {
                    args.success(data);
                },
                error: function (xhr, textStatus, errorThrown) {
                    bootbox.dialog({
                        message: xhr.responseText,
                        title: lang.messages_error,
                        buttons: {
                            danger: {
                                label: lang.close,
                                className: "red"
                            }
                        }
                    });
                },
                dataType: "json",
                type: "post"
            })
            return false;

        }
        ,
        deleteForm2: function (args) {

            //My.clearToolTip();
//                $(args.element).confirmation({
//                    onConfirm: function () {
//
//                        $(args.element).html('<i class="fa fa-spin fa-spinner"></i>');
//
//                        $.ajax({
//                            url: args.url,
//                            data: args.data,
//                            success: function (data) {
//
//                                $(args.element).html('<i class="fa fa-trash fa-1-8x text-danger"></i>');
//
//                                if (data.type == 'success')
//                                {
//
//                                    $(args.element).parent().parent().fadeOut('slow');
//                                    args.success(data);
//
//                                }
//
//                            },
//                            error: function (xhr, textStatus, errorThrown) {
//                                $(args.element).html('<i class="fa fa-trash fa-1-8x text-danger"></i>');
//
//                                $('.loading').addClass('hide');
//                                bootbox.dialog({
//                                    message: xhr.responseText,
//                                    title: lang.messages_error,
//                                    buttons: {
//                                        danger: {
//                                            label: lang.close,
//                                            className: "red"
//                                        }
//                                    }
//                                });
//                            },
//                            dataType: "json",
//                            type: "post"
//                        })
//
//                        return false;
//                    }
//                }).confirmation({'trigger': 'click'});
        }
        ,
        deleteForm: function (args) {

            $(args.element).html('<i class="fa fa-spin fa-spinner"></i>');
            $.ajax({
                url: args.url,
                data: args.data,
                success: function (data) {
                    console.log(data);


                    if (data.type == 'success') {

                        $(args.element).closest('tr').fadeOut('slow');
                        args.success(data);

                    } else {
                        $(args.element).html('<i class="fa fa-times fa-1-8x text-danger"></i>');
                        bootbox.dialog({
                            message: '<p>' + data.message + '</p>',
                            title: 'رسالة تنبيه',
                            buttons: {
                                danger: {
                                    label: 'اغلاق',
                                    className: "red"
                                }
                            }
                        });
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $(args.element).html('<i class="fa fa-trash fa-1-8x text-danger"></i>');
                    $('.loading').addClass('hide');
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "post"
            })

        }
        ,
        multiDeleteForm: function (args) {

            My.clearToolTip();

            if ($(args.element).hasClass("has-confirm")) {
                $(args.element).confirmation('show');
                return false;
            }
            $(args.element).addClass("has-confirm");
            $(args.element).confirmation({
                href: "javascript:;",
                onConfirm: function () {

                    $.ajax({
                        url: config.site_url + args.url,
                        data: args.data,
                        success: function (data) {

                            if (data.type == 'success')
                            {
                                args.success(data);
                                $(args.element).prop("disabled", true);
                                $('#delete-num').html(0);
                                $('#checkAll').prop("indeterminate", false).parent().removeClass("indeterminate");

                            } else {
                                bootbox.dialog({
                                    message: data.message,
                                    title: lang.messages_error,
                                    buttons: {
                                        danger: {
                                            label: lang.close,
                                            className: "red"
                                        }
                                    }
                                });
                            }

                        },
                        error: function (xhr, textStatus, errorThrown) {

                            $('.loading').addClass('hide');
                            bootbox.dialog({
                                message: xhr.responseText,
                                title: lang.messages_error,
                                buttons: {
                                    danger: {
                                        label: lang.close,
                                        className: "red"
                                    }
                                }
                            });
                        },
                        dataType: "json",
                        type: "post"
                    })

                    return false;
                }
            }).confirmation('show');
        }
        ,
    }
    ;

}();

jQuery(document).ready(function () {
    My.init();
});

