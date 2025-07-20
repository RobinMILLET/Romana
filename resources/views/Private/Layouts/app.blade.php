<!DOCTYPE html>
<html lang="fr">
@stack('scripts')
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Romana')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/private.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body id="app" class="flex flex-col min-h-screen">
    @include('Private.Layouts.navbar')
    
    <main class="flex-grow bg-grey">
        @yield('content')
    </main>
</body>
</html>