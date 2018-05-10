var Games = function () {

    var init = function () {
        handleFilter();


    }
    var handleFilter = function () {
        $('.filter-item').on('change', function () {
            var data = $("#filter-form").serializeArray();


            var url = config.url + "/games";
            var params = {};
            $.each(data, function (i, field) {
                var name = field.name;
                var value = field.value;
                if (value) {
                    params[field.name] = field.value
                }

            });
            var query = $.param(params);
            url += '?' + query;

            window.location.href = url;

        });


    }
   

    return {
        init: function () {
            init();
        }
        ,

    }


}();

$(document).ready(function () {
    Games.init();
});