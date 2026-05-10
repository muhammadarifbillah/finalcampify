<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campify</title>
    @vite('resources/css/app.css')
    @yield('extra_css')
</head>
<body>

    {{-- Navbar --}}
    @include('layouts.navbar_pembeli')

    {{-- Content --}}
    <main class="{{ request()->routeIs('home') ? '' : 'pt-24' }}">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.footer_pembeli')

    @yield('extra_js')
</body>
</html>