<!DOCTYPE html>
@php
use App\Models\Langue;
@endphp
<html lang="{{ session('locale', Langue::find(0))->langue_code }}">
@stack('scripts')
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Romana')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body id="app" class="flex flex-col min-h-screen">
    @include('Layouts.navbar')

    <main class="flex-grow bg-grey">
        @yield('content')
    </main>

    @include('Layouts.footer')
</body>
</html>