<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Romana')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/private.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body id="app" class="min-h-screen flex items-center justify-center p-4">
    <img id="login-logo" src="{{ asset('public/images/RomanaW.png') }}" alt="Logo" class="h-10 logo">
    <div class="bg-neutral-900 w-full max-w-md rounded-2xl shadow-xl p-8 space-y-4">
        <div class="text-center">
            <h1 class="text-3xl font-bold">Espace Administrateur</h1>
            <p class="text-sm mt-1">Connexion requise pour accéder à la gestion.</p>
        </div>

        <form method="POST" action="{{ route('api.login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="login" class="block text-sm font-medium">Nom du compte</label>
                <input type="text" name="login" id="login" maxlength="64" required autofocus
                       class="bg-neutral-600 mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:ring focus:ring-white focus:outline-none border-gray-300">
            </div>

            <div>
                <label for="mdp" class="block text-sm font-medium">Mot de passe</label>
                <input type="password" name="mdp" id="mdp" maxlength="64" required
                       class="bg-neutral-600 mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:ring focus:ring-white focus:outline-none border-gray-300">
            </div>

            <button type="submit"
                    class="w-full bg-neutral-800 hover:bg-neutral-600 text-back font-semibold py-2 px-4 rounded-lg transition duration-200">
                Connexion
            </button>
        </form>

        @if (session('errors'))
            @if (session('errors')->first('503'))
                <p class="text-center space-y-1 red rounded-md">La connexion a échoué</p>
            @endif
        @endif
    </div>
</body>
</html>
