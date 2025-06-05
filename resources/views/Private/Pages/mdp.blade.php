@php
    use App\Http\Controllers\AuthController;
    use App\Models\Constante;
    [$nb_chars, $nb_majs, $nb_mins, $nb_nums, $nb_spe] = Constante::key('mdp_critères');
    if (!isset($classes)) $classes = ['', '', '', '', ''];
    if (!isset($error)) $error = null;
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Romana')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/private.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body id="app" class="bg-black min-h-screen flex items-center justify-center p-4">
    <img id="login-logo" src="{{ asset('public/images/RomanaW.png') }}" alt="Logo" class="h-10 logo">
    <div class="bg-neutral-900 w-full max-w-xl rounded-2xl shadow-xl p-8 space-y-4">
        <div class="text-center">
            <h1 class="text-3xl font-bold">Changement de mot de passe</h1>
            @if (AuthController::current()->personnel_mdp_change === null)
                <p class="text-sm mt-1">Pour des raisons de sécurité, vous devez définir un nouveau mot de passe.</p>
            @endif
        </div>

        <form method="POST" action="{{ route('api.mdp') }}" class="space-y-6">
            @csrf

            <div>
                <label for="password1" class="block text-sm font-medium">Nouveau mot de passe</label>
                <input type="password" name="password1" id="password1" maxlength="64" required
                       class="bg-neutral-600 mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:ring focus:ring-white focus:outline-none border-gray-300">
            </div>

            <div>
                <label for="password2" class="block text-sm font-medium">Confirmer le mot de passe</label>
                <input type="password" name="password2" id="password2" maxlength="64" required
                       class="bg-neutral-600 mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:ring focus:ring-white focus:outline-none border-gray-300">
            </div>

            <ul class="text-sm text-wite bg-neutral-700 p-3 rounded-md border border-gray-500">
                @if ($nb_chars != 0)
                <li class="{{ $classes[0] }}">Minimum {{ $nb_chars }} caractère(s)</li>
                @endif
                @if ($nb_majs != 0)
                <li class="{{ $classes[1] }}">Au moins {{ $nb_majs }} majuscule(s)</li>
                @endif
                @if ($nb_mins != 0)
                <li class="{{ $classes[2] }}">Au moins {{ $nb_mins }} minuscule(s)</li>
                @endif
                @if ($nb_nums != 0)
                <li class="{{ $classes[3] }}">Au moins {{ $nb_nums }} chiffre(s)</li>
                @endif
                @if ($nb_spe != 0)
                <li class="{{ $classes[4] }}">Au moins {{ $nb_spe }} caractère(s) spécial(aux)</li>
                @endif
            </ul>

            <button type="submit"
                    class="w-full bg-neutral-800 hover:bg-neutral-600 text-back font-semibold py-2 px-4 rounded-lg transition duration-200">
                Mettre à jour le mot de passe
            </button>
        </form>
        
        @switch ($error)
            @case ('Unique')
                <p class="text-center space-y-1 red rounded-md">Vous ne pouvez pas utiliser le même mot de passe.</p>@break
            @case ('Unequal')
                <p class="text-center space-y-1 red rounded-md">Les deux mots de passes ne sont pas les mêmes.</p>@break
            @case ('SQL')
                <p class="text-center space-y-1 red rounded-md">Erreur : Une erreur technique s'est produite...</p>@break
        @endswitch
    </div>
</body>
</html>