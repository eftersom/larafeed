<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons -->
    <link href="{{ config('larafeed.favicon', '/vendor/larafeed/icons/favicon.ico') }}" rel="icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('larafeed.name', 'Lara-feed') }}</title>

    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Scripts -->
    <script src="{{ asset('vendor/larafeed/js/larafeed.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('vendor/larafeed/css/larafeed.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/larafeed') }}">
                    {{ config('larafeed.name', 'Lara-feed') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobile-menu" aria-controls="mobile-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div id="mobile-menu" class="collapse navbar-collapse p-3">
                    @guest
                        <li class="dropdown-item">
                            <a class="nav-link" href="/login">{{ __('larafeed::general.sign_in') }}</a>
                        </li>
                    @else
                        <a class="dropdown-item" href="{{ route('feed-show-all') }}">
                            {{ __('larafeed::general.my_feeds') }}
                        </a>
                        <a class="dropdown-item mt-3" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </div>

                <ul id="desktop-menu" class="navbar-nav ml-auto d-none d-md-block">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="/login">{{ __('larafeed::general.sign_in') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('feed-show-all') }}">
                                    {{ __('larafeed::general.my_feeds') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <main id="larafeed" class="py-4">
            <div class="container">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @if ($level >= 1)
                            <li class="breadcrumb-item"><a href="/larafeed" class="text-decoration-none {{ $page === 'home' ? 'active' : ''}}">{{ __('larafeed::breadcrumbs.home') }}</a></li>
                        @endif

                        @if ($level >= 2)
                            <li class="breadcrumb-item "><a href="{{ $previous }}" class="text-decoration-none {{ $page === 'feed' ? 'active' : ''}}">{{ __('larafeed::breadcrumbs.user_feed') }}</a></li>
                        @endif

                        @if ($level >= 3)
                            <li class="breadcrumb-item"><a href="{{ $previous }}" class="text-decoration-none {{ $page === 'all' ? 'active' : ''}}">{{ __('larafeed::breadcrumbs.show_feed') }}</a></li>
                        @endif
                    </ol>
                </nav>            
            </div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        @if(isset($errors)) 
                            @if($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    {!! implode('', $errors->all('<div>:message</div>')) !!}
                                </div>
                            @endif
                            @foreach (array('danger', 'warning', 'success', 'info') as $key)
                                @if(Session::has($key))
                                    <p class="alert alert-{{ $key }}">{{ Session::get($key) }}</p>
                                @endif
                            @endforeach
                        @endif
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
