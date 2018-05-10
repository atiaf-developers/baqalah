<!doctype html>
<html>
    <head>

        @include('components/front/meta')

    </head>

    <body>

        @include('components/front/header')

        @yield('content')

        @include('components/front/footer')

        @include('components/front/scripts')


    </body>
</html>
