<!doctype html>
<html>
    <head>

        @include('components/front/meta')

    </head>

    <body id="top">

        @include('components/front/header')
        <div class="wrapper">
            <section id="profile">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="title">
                                    <h2>@yield('pageTitle')</h2>
                                </div>
                                <div class="profile-area">
                                    <div class="col-md-12">
                                        <div class="col-md-9">
                                                  @yield('content')
                                        </div>
                                        <div class="col-md-3">
                                             @include('components/front/profile/sidebar')

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        </div>

        @include('components/front/footer')

        @include('components/front/scripts')


    </body>
</html>
