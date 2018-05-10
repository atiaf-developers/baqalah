var App = function () {

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

    // theme layout color set

    var brandColors = {
        'blue': '#89C4F4',
        'red': '#F3565D',
        'green': '#1bbc9b',
        'purple': '#9b59b6',
        'grey': '#95a5a6',
        'yellow': '#F8CB00'
    };



    return {
        //main function to initiate the theme
        init: function () {
        },
        countDownMinute: function (minutes) {
            var timer2 = minutes + ":00";
            var interval = setInterval(function () {


                var timer = timer2.split(':');
                //by parsing integer, I avoid all extra string processing
                var minutes = parseInt(timer[0], 10);
                var seconds = parseInt(timer[1], 10);
                --seconds;
                minutes = (seconds < 0) ? --minutes : minutes;
                if (minutes < 0) {
                    clearInterval(interval);
                } else {
                    seconds = (seconds < 0) ? 59 : seconds;
                    seconds = (seconds < 10) ? '0' + seconds : seconds;
                    //minutes = (minutes < 10) ?  minutes : minutes;
                    $('.countdown').html(minutes + ':' + seconds);
                    timer2 = minutes + ':' + seconds;
                }


            }, 1000);
        },
        getParameterByName: function (name, url) {
            if (!url)
                url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
            if (!results)
                return null;
            if (!results[2])
                return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        },
        removeUrlParameter: function (key, sourceURL) {
            var rtn = sourceURL.split("?")[0],
                    param,
                    params_arr = [],
                    queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
            if (queryString !== "") {
                params_arr = queryString.split("&");
                for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                    param = params_arr[i].split("=")[0];
                    if (param === key) {
                        params_arr.splice(i, 1);
                    }
                }
                rtn = rtn + "?" + params_arr.join("&");
            }
            return rtn;
        },
        getUrlParameter: function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        },
        toolTipHtml: function (message) {
            var html = '<div class="tooltip bottom in" style="font-size: 14px; top: 44px; left: 20px; display: block;">' +
                    '<div class="tooltip-arrow  wsool-tooltip-arrow"></div>' +
                    '<div class="tooltip-inner  wsool-tooltip">' + message + '</div>' +
                    '</div>';
            return html;
        },
        validate: function ($form) {
            $(document).off('change', '.has-validate');
            $(document).on('change', '.has-validate', function () {
                $(this).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(this).closest('.form-group').find('.form-error').html('');
                $(this).removeClass('has-validate');
            });
            errorElements2 = new Array;
            $.each($form.find('[data-customerror=true]:visible'), function () {

                this.message = $(this).attr('data-customerror-message');
                errorElements2.push($(this));

            });
            $.each($form.find('input[type=text].required:visible,select.required:visible,textarea.required:visible'), function () {
                var thisVal = $(this).val();

                if (!thisVal || thisVal.replace(/ /g, '') === '') {
                    error = true;
                    this.message = lang.required;
                    errorElements2.push($(this));
                }
            });
            $.each($form.find('input[type=checkbox].required:visible'), function () {
                if (!$(this).is(':checked')) {

                    this.message = lang.pleaseReadAndAcceptTerms;
                    errorElements2.push($(this));
                }
            });
            $.each($form.find('.emailInput:visible'), function () {

                if ($(this).val() && !validateEmail($(this).val())) {

                    error = true;
                    this.message = lang.email_not_valid;
                    errorElements2.push($(this));

                }


            });
            $.each($form.find('.requiredCheckboxes:visible'), function () {
                var err = true;
                $.each($(this).find('input[type=checkbox]'), function () {
                    if ($(this).is(':checked')) {
                        err = false;
                    }
                });

                if (err) {

                    this.message = lang.you_have_to_select_one_at_least;
                    errorElements2.push($(this));
                }

            });
            $.each($form.find('.requiredRadios:visible'), function () {
                var err = true;
                $.each($(this).find('input[type=radio]'), function () {
                    if ($(this).is(':checked')) {

                        err = false;
                    }
                });

                if (err) {


                    this.message = lang.you_have_to_select_one_at_least;
                    errorElements2.push($(this));
                }

            });
            $.each($form.find('.ckEditorRequired:visible'), function () {
                if (CKEDITOR.instances[$(this).attr('data-editorid')] && CKEDITOR.instances[$(this).attr('data-editorid')].getData().replace(/ /g, '') === '') {
                    this.message = lang.required;
                    errorElements2.push($(this));
                }
            });
            if (errorElements2.length > 0) {

                function compare(a, b) {
                    if (a.offset().top < b.offset().top)
                        return -1;
                    else if (a.offset().top > b.offset().top)
                        return 1;
                    else
                        return 0;
                }

                errorElements2.sort(compare);

                $.each(errorElements2, function () {
                    $(this).closest('.form-group').removeClass('has-success').addClass('has-error');
                    var html = '<div class="tooltip bottom in" style="font-size: 14px; top: 44px; left: 20px; display: block;">' +
                            '<div class="tooltip-arrow  wsool-tooltip-arrow"></div>' +
                            '<div class="tooltip-inner  wsool-tooltip">' + this[0].message + '</div>' +
                            '</div>';
                    $(this).closest('.form-group').find('.form-error').html(html);
                    $(this).addClass('has-validate');
                });

                return false;
            }

            return true;
        },
        scrollToTopWhenFormHasError: function () {
            var $container = "html,body";
            var $scrollContainer = window;
            var containerOffsetTop = $($container).offset().top;
            var containerScrollTop = $($scrollContainer).scrollTop();
            if ($($container).is("html")) {
                containerOffsetTop = containerScrollTop;
            }
            console.log(errorElements[0], Math.floor(errorElements[0].offset().top - containerOffsetTop), (containerScrollTop));
            if (containerScrollTop > (Math.floor(errorElements[0].offset().top - containerOffsetTop) - 10)) {
                console.log('mustScroll');
                console.log(containerScrollTop - (Math.floor(errorElements[0].offset().top - containerOffsetTop) - 10));
                $($container).animate({scrollTop: containerScrollTop + (Math.floor(errorElements[0].offset().top - containerOffsetTop) - 10)});

            }
        },
        ajaxGoTo: function (url, pushState) {
            window.location.href = url;
            return false;
            redirect = '';
            ajaxRedirect = '';
            redirectDelay = 3000;
            clearTimeout(redirectTimeout);
            $('.htmlpopup').hide();
            $("html").removeClass('noscroll');
            if (xhr) {
                xhr.abort();
            }

            if (preventPageChange) {

                return false;
            }
            if (currentLoggedin !== liveLoggedin) {
                window.location.href = url;
                return false;
            }

            if (pushState) {
                history.pushState({'url': url}, null, url);
            }
            if (ajaxGoToXHR) {
                ajaxGoToXHR.abort();
            }
            $(".ajaxLoadingProgressBar").show().css({'width': '0%'});
            url = 'ajax' + url;

            ajaxGoToXHR = $.ajax({
                xhr: function ()
                {
                    var xhr = new window.XMLHttpRequest();
                    xhr.addEventListener("progress", function (evt) {

                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            $(".ajaxLoadingProgressBar").css({'width': percentComplete + '%'});
                        }
                    }, false);

                    return xhr;
                },
                type: "POST",
                data: {},
                url: url

            }).done(function (msg) {
                $(document).off('pageLoad');
                $(document).on('pageLoad', function () {
                    pageLoadFunctions();
                });

                $(".cpShow").hide();
                $(".cpHide").show();
                $("#pageContentBodyScroller").html(msg);
                $(document).trigger('pageLoad');

                if (window.scrollY > 250) {
                    $('html,body').animate({scrollTop: 250}, 'fast');
                }
                fixScroll();
                window.setTimeout(function () {
                    $(".ajaxLoadingProgressBar").fadeOut();
                }, 500);
                processMaps();

                $(".ImagePreview").hide();

                if (window.location.hash) {
                    var hash = window.location.hash;
                    var aTag = $("a[name='" + hash.replace('#', '') + "']");
                    $('html,body').animate({scrollTop: aTag.offset().top}, 'fast');

                }
            });
        }
        ,
        ajax_error_message: function (xhr) {
            var message;
            if (xhr.status == 401) {
                message = lang.you_must_login_first;
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

        },
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
        },
        set_errors: function (errors) {
            for (var i in errors)
            {
                App.set_error(i, errors[i]);
            }
        },
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
        },
        emptyForm: function () {
            // $('[data-role="tagsinput"]').tagsinput('removeAll');
            $('input[type="text"],input[type="email"],input[type="date"],input[type="password"],input[type="number"],textarea').val("");
            //$('input[type="checkbox"]').prop("checked", false).trigger("change");
        },
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
        },
        setModalTitle: function (id, title)
        {
            $(id + 'Label').html(title);
        },
        clearToolTip: function () {
            $('.tooltip.fade').each(function () {
                if ($(this).attr("id"))
                {
                    var $id = $(this).attr("id");
                    $('[aria-describedby="' + $id + '"]').removeAttr("aria-describedby");
                    $(this).remove()
                }
            })
        },
        editForm: function (args) {

            //App.clearToolTip();


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
                    App.ajax_error_message(xhr);

                },
                dataType: "json",
            })
            return false;

        },
        clearFormErrors: function () {
            $('.has-error').removeClass("has-error");
            $('.help-block').html("");
        },
        Ajax: function (args) {

            App.clearToolTip();

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

        },
        deleteForm2: function (args) {

            //App.clearToolTip();
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
        },
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
                    App.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "post"
            })

        },
        multiDeleteForm: function (args) {

            App.clearToolTip();

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
        },
    };

}();

jQuery(document).ready(function () {
    App.init();
});

