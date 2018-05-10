<!doctype html>
<html>
    <head>

        @include('components/front/meta')

    </head>

    <body id="top">

        @include('components/front/header')
        <div class="wrapper">

            @yield('content')

        </div>

        @include('components/front/footer')

        @include('components/front/scripts')


    </body>
</html>
