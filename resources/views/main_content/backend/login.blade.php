<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Keswa</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #1 for " name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{url('public/backend/plugins')}}/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('public/backend/plugins')}}/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('public/backend/plugins')}}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('public/backend/plugins')}}/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{url('public/backend/plugins')}}/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="{{url('public/backend/plugins')}}/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{url('public/backend/css')}}/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{url('public/backend/css')}}/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="{{url('public/backend/css')}}/login-4.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="{{url('public/backend/images')}}/favicon.png" />
        <script>
            var config = {
                admin_url: " {{ url('admin') }}",
                asset_url: " {{ url('public//') }}",
            };
            var lang = {

            };

            // alert(config.lang);
        </script>
    </head>
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="{{url('admin/login')}}">
                <img src="{{url('public/backend/images/logo.png')}}" alt="" /> </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <div id="alert-message" class="alert-danger text-center">

            </div>
            <br>
            <form class="login-form"  id="login-form"  method="post"  action="{{ route('admin.login.submit') }}">
                {{ csrf_field() }}
                <h3 class="form-title">Login</h3>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> Enter any username and password. </span>
                </div>

                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">{{_lang('app.username')}}</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="{{_lang('app.username')}}" name="username" id="username" />
                    </div>
                    <div class="help-block"></div>

                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">{{_lang('app.password')}}</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="{{_lang('app.password')}}" name="password" id="password" />
                    </div>
                    <div class="help-block"></div>
                </div>
                <div class="form-actions">
                    <label class="rememberme mt-checkbox mt-checkbox-outline">
                        <input type="checkbox" name="remember" value="1" /> Remember me
                        <span></span>
                    </label>
                    <button type="submit" class="btn green pull-right submit-form"> Login </button>
                </div>
            </form>
            <!-- END LOGIN FORM -->

        </div>
        <!-- END LOGIN -->
        <!-- BEGIN COPYRIGHT -->
        <div class="copyright">
                    All Rights Reserved ©    Co. 2018 | <a target="_blank" href="http://www.atiafco.com/">Powered By Atiafco </a>
        </div>
        <!-- END COPYRIGHT -->
        <!--[if lt IE 9]>
<script src="{{url('public/backend/plugins')}}/respond.min.js"></script>
<script src="{{url('public/backend/plugins')}}/excanvas.min.js"></script>
<script src="{{url('public/backend/plugins')}}/ie8.fix.min.js"></script>
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{url('public/backend/plugins')}}/jquery.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/bootbox/bootbox.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/js.cookie.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{url('public/backend/plugins')}}/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/plugins')}}/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{url('public/backend/scripts')}}/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->

        <script src="{{url('public/backend/scripts')}}/app.js" type="text/javascript"></script>
        <script src="{{url('public/backend/scripts')}}/login-4.min.js" type="text/javascript"></script>
        <script src="{{url('public/backend/js')}}/my.js" type="text/javascript"></script>
        <script src="{{url('public/backend/js')}}/login.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>