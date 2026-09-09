<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Amber') — {{ config('app.name', 'Amber') }}</title>

        <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
            <link href="{{ asset('vendor/larajax/ui/ui.css') }}" rel="stylesheet">
            <style>
                body { background-color: #FDFDFC; color: #1b1b18; }
                .amber-nav { border-bottom: 1px solid #e5e5e2; }
                .amber-nav .nav-link.active { font-weight: 600; }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

            <script src="{{ asset('vendor/larajax/framework-bundle.js') }}"></script>
            <script type="module" src="{{ asset('vendor/larajax/ui/ui.js') }}"></script>
        @endif
    </head>
    <body>
        <nav class="amber-nav px-5 py-3 mb-4">
            <div class="d-flex align-items-center gap-4">
                <a class="navbar-brand fw-bold text-decoration-none" href="{{ url('users') }}">Amber</a>
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users', 'users/*') ? 'active' : '' }}" href="{{ url('users') }}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users/structure') ? 'active' : '' }}" href="{{ url('users/structure') }}">Structure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('user-groups', 'user-groups/*') ? 'active' : '' }}" href="{{ url('user-groups') }}">Groups</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="px-5 pb-5">
            @yield('content')
        </div>
    </body>
</html>
