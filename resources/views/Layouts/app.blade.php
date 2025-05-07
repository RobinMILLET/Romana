<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Romana')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen">
    @include('Layouts.navbar')

    <main class="flex-grow bg-grey">
        @yield('content')
    </main>

    @include('Layouts.footer')
</body>
</html>