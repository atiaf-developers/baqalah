var _config;
var address = 0;
var Cart = function () {

    var init = function () {
        handleAddToCart();
        handleUpdateQuantity();
        handleRemoveFromCart();
        handleChangeCoupon();
        handleEditAddressSubmit();
        handleNewOrderSubmit();

    }

    var handleEditAddressSubmit = function () {
        $('.edit-address-submit').on('click', function () {
            address = $('.address-one:checked').val();

            $(this).prop('disabled', true);
            $(this).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');

            var url = config.url + "/getAddress/" + address;
            setTimeout(function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data)
                    {

                        $('.edit-address-submit').prop('disabled', false);
                        $('.edit-address-submit').html(lang.save);
                        if (data.type == 'success') {
                            $('#editAddressModal').modal('hide');
                            $('#selected-address').html(data.message.long_address);
                        } else {

                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);

                        }


                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $('.edit-address-submit').prop('disabled', false);
                        $('.edit-address-submit').html(lang.save);
                        App.ajax_error_message(xhr);
                    },
                });

            }, 1000);




        });
    }
    var handleChangeCoupon = function () {
        $('#coupon').on('change', function () {
            var val = $(this).val();
            var url = config.url + "/cart/coupon-check?coupon=" + val;

            $('#loader').show();
            $('#cart-content').addClass('loading');
            setTimeout(function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data)
                    {
                        console.log(data);
                        $('#loader').hide();
                        $('#cart-content').removeClass('loading');
                        if (data.type == 'success') {
                            if (data.data.items.length == 0) {
                                $('#cart-content').html(contentForEmptyCart());
                            } else {
                                var PriceList = data.data.price_list;
                                for (var i in PriceList) {
                                    $('#' + i).html(PriceList[i] + ' ' + lang.currency_sign)
                                }
                                $('#coupon')
                                        .closest('.form-group').removeClass('has-error').addClass("has-info");
                                $('#coupon').closest('.form-group').find(".help-block").html('')

                            }

                        } else {

                            $('#coupon')
                                    .closest('.form-group').addClass('has-error').removeClass("has-info");
                            $('#coupon').closest('.form-group').find(".help-block").html(data.message)

                        }


                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $('#addToCartForm .submit-form').prop('disabled', false);
                        $('#addToCartForm .submit-form').html(lang.save);
                        App.ajax_error_message(xhr);
                    },
                });

            }, 1000);




        });
    }
    var handleUpdateQuantity = function () {
        $('.btn-qty-plus,.btn-qty-minus').on('click', function () {
            var type = $(this).data('type');
            var index = $(this).data('index');
//            var ele;
//            if (type == 'plus') {
//                ele = 'btn-qty-plus';
//            }
//            if (type == 'minus') {
//                ele = 'btn-qty-minus';
//            }
//            var index = $('.' + ele).index(this);
            var quantity = parseInt($('input[name="qty[' + index + ']"]').val());
            var url = config.url + "/cart/update-quantity?index=" + index + '&qty=' + quantity;

            $('#loader').show();
            $('#cart-content').addClass('loading');
            setTimeout(function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data)
                    {
                        console.log(data);
                        $('#loader').hide();
                        $('#cart-content').removeClass('loading');
                        if (data.type == 'success') {
                            if (data.data.items.length == 0) {
                                $('#cart-content').html(contentForEmptyCart());
                            } else {
                                var PriceList = data.data.price_list;
                                for (var i in PriceList) {
                                    $('#' + i).html(PriceList[i] + ' ' + lang.currency_sign)
                                }
                            }

                        } else {



                        }


                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $('#addToCartForm .submit-form').prop('disabled', false);
                        $('#addToCartForm .submit-form').html(lang.save);
                        App.ajax_error_message(xhr);
                    },
                });

            }, 1000);




        });
    }

    var contentForEmptyCart = function () {
        var html = '<div class="col-md-12">' +
                '<div class="orderbasc">' +
                '<i class="fa fa-shopping-cart" style="font-size:80px; display:block; text-align: center; margin:20px auto; color: #d5344a;" aria-hidden="true"></i>' +
                '<h1 style="text-align:center; color:#000; font-size:20px; margin:30px 0;" >السلة فارغة</h1>' +
                '</div>' +
                '</div>';
        return html;
    }
    var handleRemoveFromCart = function () {
        $('.remove-cart').on('click', function () {
            var ele = $(this);
//            var index = $('.remove-cart').index(this);
            var index = $('.remove-cart').data('index');
            var url = config.url + "/cart/" + index + '/remove';
            $('#loader').show();
            $('#cart-content').addClass('loading');
            setTimeout(function () {

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data)
                    {
                        $('#loader').hide();
                        $('#cart-content').removeClass('loading');
                        if (data.type == 'success') {
                            if (data.data.items.length == 0) {
                                $('#cart-content').html(contentForEmptyCart());

                            } else {
                                ele.closest('.item-row').remove();
                                var PriceList = data.data.price_list;
                                for (var i in PriceList) {
                                    $('#' + i).html(PriceList[i] + ' ' + lang.currency_sign)
                                }
                            }

                        } else {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);


                        }


                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $('#addToCartForm .submit-form').prop('disabled', false);
                        $('#addToCartForm .submit-form').html(lang.save);
                        App.ajax_error_message(xhr);
                    },
                });

            }, 1000);




        });
    }
    var handleAddToCart = function () {
        $("#addToCartForm").validate({
            rules: {
            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('');

            },
            errorPlacement: function (error, element) {
                errorElements1.push(element);
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });
        $('#addToCartForm .submit-form').click(function () {
            var validate_2 = $('#addToCartForm').validate().form();
            if (validate_2) {
                $('#addToCartForm .submit-form').prop('disabled', true);
                $('#addToCartForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addToCartForm').submit();

                }, 1000);

            }
            return false;
        });

        $('#addToCartForm input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#addToCartForm').validate().form();
                if (validate_2) {

                    $('#addToCartForm .submit-form').prop('disabled', true);
                    $('#addToCartForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addToCartForm').submit();
                    }, 1000);

                }

                return false;
            }
        });
        $('#addToCartForm').submit(function () {
            var url = config.url + "/cart";
            var formData = new FormData($(this)[0]);
            formData.append('meal_id', _config.meal_id);
            formData.append('resturant_id', _config.resturant_id);
            formData.append('resturant_branch_id', _config.resturant_branch_id);
            formData.append('service_charge', _config.service_charge);
            formData.append('delivery_cost', _config.delivery_cost);
            formData.append('vat', _config.vat);
            formData.append('resturant_slug', _config.resturant_slug);
            if ($('input[name="size"]').length > 0) {
                var index = $('input[name="size"]:checked').index('input[name="size"]');
                var size_id = $('input[name="size"]:checked').val();
                var quantity = $('input[name="sqty[' + index + ']"]').val();
                formData.append('size_id', size_id);
                formData.append('quantity', quantity);
            } else {
                var quantity = $('input[name="mqty').val();
                formData.append('quantity', quantity);
            }


            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    $('#addToCartForm .submit-form').prop('disabled', false);
                    $('#addToCartForm .submit-form').html(lang.save);
                    $('#addToCartModal').modal('hide');
                    if (data.type == 'success') {
                        $('#addToCartModal').on('hidden.bs.modal', function () {
                            $('.alert-danger').hide();
                            $('.alert-success').show().find('.message').html(data.message);
                        })

                    } else {
                        $('#addToCartModal').on('hidden.bs.modal', function () {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        })


                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addToCartForm .submit-form').prop('disabled', false);
                    $('#addToCartForm .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);
                },
            });

            return false;
        });

    }
    var handleNewOrderSubmit = function () {

        $('#newOrderForm .submit-form').on('click', function () {

            $(this).prop('disabled', true);
            $(this).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
            setTimeout(function () {
                $('#newOrderForm').submit();

            }, 1000);

            return false;
        });

        $('#newOrderForm').submit(function () {
            var url = config.url + "/cart/new-order";
            var formData = new FormData($(this)[0]);
            if (address != 0) {
                formData.append('address', address);
            }



            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    $('#newOrderForm .submit-form').prop('disabled', false);
                    $('#newOrderForm .submit-form').html(lang.save);
                    console.log(data);
                    if (data.type == 'success') {

                        $('#newMessageModal .message-content').html(lang.request_sent_successfully);
                        $('#newMessageModal').modal('show');
                        $('#newMessageModal').on('hidden.bs.modal', function () {
                            window.location.href = config.url;
                        })

                    } else {
                        $('.alert-success').hide();
                        $('.alert-danger').show().find('.message').html(data.message);


                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#newOrderForm .submit-form').prop('disabled', false);
                    $('#newOrderForm .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);
                },
            });

            return false;
        });



    }


    return {
        init: function () {
            init();
        },
        goToNext: function (el) {

            if (config.isUser) {
                window.location.href = config.url + '/cart?step=2'
            } else {
                $('#newMessageModal .message-content').html(lang.you_must_login_first);
                $('#newMessageModal').modal('show');
            }

        },
        addToCart: function (el) {
            _config = $(el).data('config');
            //console.log(mealConfig);
            $('#addToCartModal').modal('show');
        },
        showAddresses: function () {
            $('#editAddressModal').modal('show');
        },
        removeFromCart: function (el) {
            var index = $('.remove-cart').index(el);
            var url = config.url + "/cart/" + index + '/remove';
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data)
                {
                    console.log(data);

                    if (data.type == 'success') {
                        if (data.data.items.length == 0) {
                            $('#cart-content').html('');
                        } else {
                            $(el).closest('.item-row').remove();
                            var PriceList = data.data.price_list;
                            for (var i in PriceList) {
                                $('#' + i).html(PriceList[i] + ' ' + lang.currency_sign)
                            }
                        }

                    } else {



                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addToCartForm .submit-form').prop('disabled', false);
                    $('#addToCartForm .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);
                },
            });
        },
        updateQty: function (el) {
            var index = $('.btn-qty').index(el);
            var quantity = parseInt($('input[name="qty[' + index + ']"]').val());
            var type = $(el).data('type');
            if (type == 'plus') {
                quantity = parseInt(quantity + 1);
            }
            if (type == 'minus') {
                alert(quantity)
                quantity = quantity - 1;
            }
            alert(quantity)
            return false;
            var url = config.url + "/cart/" + index + '/remove';
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data)
                {
                    console.log(data);

                    if (data.type == 'success') {
                        if (data.data.items.length == 0) {
                            $('#cart-content').html('');
                        } else {
                            $(el).closest('.item-row').remove();
                            var PriceList = data.data.price_list;
                            for (var i in PriceList) {
                                $('#' + i).html(PriceList[i] + ' ' + lang.currency_sign)
                            }
                        }

                    } else {



                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addToCartForm .submit-form').prop('disabled', false);
                    $('#addToCartForm .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);
                },
            });
        },
        empty: function () {
            App.emptyForm();
        },
    }


}();

$(document).ready(function () {
    Cart.init();
});