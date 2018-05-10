

var Orders = function () {

    var init = function () {

        handleReport();
    };
 
    var handleReport = function () {
        $('.btn-report').on('click', function () {
            var data = $("#orders-reports").serializeArray();


            var url = config.admin_url + "/reservations";
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



    return {
        init: function () {
            init();
        },
    };

}();
jQuery(document).ready(function () {
    Orders.init();
});

