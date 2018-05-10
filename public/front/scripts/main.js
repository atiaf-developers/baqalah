
var Main = function () {


    var init = function () {
        handlePusher();
    }



    var handlePusher = function () {
        Pusher.logToConsole = true;
        var pusher_app_key = config.pusher_app_key;
        var pusher_cluster = config.pusher_cluster;
        var pusher_encrypted = config.pusher_encrypted;
        var user_id = config.user_id;
        var isUser = config.isUser;
        var pusher = new Pusher(pusher_app_key, {
            cluster: pusher_cluster,
            encrypted: pusher_encrypted
        });

        var report = pusher.subscribe('report');
        var noti = pusher.subscribe('new_noti');
        //console.log(noti);

        noti.bind('App\\Events\\Noti', function (data) {
            console.log(data);

            if (isUser && (data.user_id == null || data.user_id == user_id)) {
                $.notify("asdf", {
                    title: data.title,
                    body: data.body,
                    icon: config.base_url + '/public/front/img/logo.png',
                }).click(function () {
                    window.location.href = data.url;
                });


            }

        });

        pusher.connection.bind('connected', function () {
            socketId = pusher.connection.socket_id;
            console.log(socketId);
        });
    }
    var handlePusherNative = function () {
        Pusher.logToConsole = true;

        var pusher = new Pusher(config.pusher_app_id, {
            cluster: config.pusher_cluster,
            encrypted: config.pusher_cluster
        });

        var report = pusher.subscribe('report');
        var violation = pusher.subscribe('violation');

        violation.bind('new_violation', function (data) {
            handleNotiSound();
            My.toast('hello');
        });
        report.bind('new_violation', function (data) {
            handleNotiSound();
        });
        pusher.connection.bind('connected', function () {
            socketId = pusher.connection.socket_id;
            console.log(socketId);
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


