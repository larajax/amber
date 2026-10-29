<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            @yield('title', 'Amber') - {{ config('app.name', 'Amber') }}
        </title>

        {{-- Applies the saved theme before the page paints --}}
        <script>
            try {
                var amberTheme = localStorage.getItem('amber-theme');
                if (amberTheme) {
                    document.documentElement.setAttribute('data-bs-theme', amberTheme);
                }
            }
            catch (e) {
            }
        </script>

        {{-- No build step: assets load from a CDN and the published vendor/larajax files. --}}
        <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="{{ asset('vendor/larajax/ui/ui.css') }}" rel="stylesheet">
        <link href="{{ asset('css/amber.css') }}" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
        <script src="{{ asset('vendor/larajax/framework-bundle.js') }}"></script>
        <script type="module" src="{{ asset('vendor/larajax/ui/ui.js') }}"></script>
        <script src="{{ asset('js/amber.js') }}" defer></script>

        {{-- Assets added by custom widgets; the page content renders before this layout, so its widgets are included --}}
        {{ Ui::renderAssets() }}
    </head>
    <body class="amber-layout">
        <div class="app-shell">
            <button
                class="sidebar-scrim"
                type="button"
                aria-label="Close navigation"
                data-amber-menu-close></button>

            @include('partials.site.sidebar')

            @include('partials.site.topbar')

            <main class="app-main">
                @yield('content')
            </main>
        </div>
    </body>
</html>
