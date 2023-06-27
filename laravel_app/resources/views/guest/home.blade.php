<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/antd/dist/antd.css" rel="stylesheet">


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/antd/dist/antd.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <h2>
                                        @if (Auth::guest())
                                            Jūs pašlaik apmeklējat mājaslapu kā viesis.
                                            @if (Route::has('login'))
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                                </li>
                                            @endif

                                            @if (Route::has('register'))
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                                </li>
                                            @endif
                                        @else
                                            <style> 
                                                .profile_image {
                                                    width:50px;
                                                    height: 50px;
                                                }
                                            </style>
                                            Sveiks {{ Auth::user()->name }} 
                                            @php $imagePath = asset('storage/images/' . auth()->user()->profile_picture_path); @endphp
                                            <img class="profile_image" src="{{ $imagePath }}" alt="{{$imagePath}}">
                                            ({{Auth::user()->role}})
                                        @endif
                                    </h2>
                                </a>
                                @if (!auth::guest())
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                                @endif
                            </li>
                        
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <ul class="image-list">
                @foreach($users as $user)
                    @foreach($user->images as $image)
                        <li>
                            <a href="{{ route('images.show', $image->id) }}" target="_blank">
                                @php $imagePath = asset('storage/images/' . $image->filename); @endphp
                                <img src="{{ $imagePath }}" alt="{{ $image->filename }}">
                            </a>
                            <span>{{ $user->name }}</span>

                            <div class="likes">
                                <span>{{ $image->likes }} likes</span>
                            </div>
                            <div class="like-buttons">
                                <form action="{{ route('images.like', $image->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Like</button>
                                </form>
                                <form action="{{ route('images.dislike', $image->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Dislike</button>
                                </form>
                            </div>

                            <span> {{$image->apraksts}} </span>
                        </li>
                    @endforeach
                @endforeach
            </ul>
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
