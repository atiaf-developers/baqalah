<!DOCTYPE html>
<html>
    <head>
        @include('components/backend/meta')
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-md">
        <div class="page-wrapper">
            @include('components/backend/header')
            <div class="page-container">
                @include('components/backend/side_bar')
                <div class="page-content-wrapper">
                    <div class="page-content">
                        @include('components/backend/breadcrumb')
                    
                            @yield('content')

               

                    </div>
                </div>

            </div>
            <div class="page-footer">
                <div class="page-footer-inner">
                    All Rights Reserved ©    Co. 2018 | <a target="_blank" href="http://www.atiafco.com/">Powered By Atiafco </a>

                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            @include('components/backend/footer')
            @yield('js')

    </body>
</html>