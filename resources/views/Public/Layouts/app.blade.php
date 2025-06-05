<!DOCTYPE html>
@php
use App\Models\Langue;
@endphp
<html lang="{{ session('locale', Langue::find(0))->langue_code }}">
@stack('scripts')
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Romana')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/public.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body id="app" class="flex flex-col min-h-screen">
    @include('Public.Layouts.navbar')

    <main class="flex-grow bg-grey">
        @yield('content')
    </main>

    @include('Public.Layouts.footer')
</body>
</html>