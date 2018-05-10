
var Main = function () {


    var init = function () {
        handleChangeLang();
     

    }





    
    var handleChangeLang = function () {
        $(document).on('change', '#change-lang', function () {
            var lang_code = $(this).val();
            var action = config.admin_url + '/change_lang';
            $.ajax({
                url: action,
                data: {lang_code: lang_code},
                async: false,
                success: function (data) {
                    console.log(data);
                    if (data.type == 'success') {

                        window.location.reload()

                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    My.ajax_error_message(xhr);
                },
                dataType: "JSON",
                type: "GET"
            });

            return false;
        });
    }




    return {
        init: function () {
            init();
        },

    }

}();

jQuery(document).ready(function () {
    Main.init();
});


