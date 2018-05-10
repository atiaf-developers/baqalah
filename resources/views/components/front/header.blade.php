<header id="logo">
    <div class="top-header">
        <div class="container">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="social">
                            <a href="{{ $settings['social_media']->facebook }}"><span class="fa fa-facebook"></span></a>
                            <a href="{{ $settings['social_media']->twitter }}"><span class="fa fa-twitter"></span></a>
                            <a href="{{ $settings['social_media']->google }}"><span class="fa fa-google-plus"></span></a>
                            <a href="{{ $settings['social_media']->youtube }}"><span class="fa fa-youtube"></span></a>
                            <a href="{{ $settings['social_media']->instagram }}"><span class="fa fa-instagram"></span></a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        @if (Auth::check())

                        <a href="{{_url('customer/dashboard')}}" class="btn btn-read"><i class="fa fa-user"></i>{{ _lang('app.profile') }}</a>

                        <a href="{{_url('customer/user/notifications')}}" class="btn btn-read"><i class="fa fa-bell"></i>{{ _lang('app.notifications') }}</a>

                       

                        @else
                        <a class="btn btn-read" href="{{_url('login?return='.base64_encode(request()->getPathInfo() . (request()->getQueryString() ? ('?' . request()->getQueryString()) : ''))) }}"><i class="fa fa-user"></i>{{_lang('app.login')}}</a>
                        @endif

                        <a href="{{url($next_lang_code.'/'.substr(Request()->path(), 3))}}" class="btn btn-read"><i class="fa fa-flag-o"></i>{{$next_lang_text}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-header">
        <div class="container">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="logo-mobadra">
                            <a href="{{_url('')}}">
                                <img class="logo" src="{{url('public/front/img')}}/logo2.png" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="logo-keswa">
                            <a href="{{_url('')}}">
                                <img class="logo-2" src="{{url('public/front/img')}}/logo.png" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<section id="navbar">
    <div class="container">
        <div class="navbar-header">
            <!-- responsive nav button -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- /responsive nav button -->

        </div>
        <nav class="collapse navbar-collapse navbar-right" role="navigation">
            <ul id="nav" class="nav navbar-nav menu">
                <li><a href="{{ url('/') }}">{{ _lang('app.home') }}</a></li>
                <li><a href="{{ route('about_us') }}">{{ _lang('app.about_us') }}</a></li>
                <li><a href="{{ route('news_events') }}">{{ _lang('app.news_and_events') }}</a></li>
                <li><a href="{{ route('corporation_activities') }}">{{ _lang('app.corporation_activities') }}</a></li>
                <li><a href="{{ route('gallary') }}">{{ _lang('app.photos') }}</a></li>
                <li><a href="{{ route('video_gallary') }}">{{ _lang('app.videos') }}</a></li>
                <li><a href="{{ _url('') }}#contact-form">{{ _lang('app.contact_us') }}</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b class="caret"></b>{{ _lang('app.others') }} </a> 

                    <ul class="dropdown-menu">
                        @foreach ($others as $item)
                        <li><a href="{{ route('others',$item->slug) }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </li>


            </ul>
        </nav>
    </div>
</section>