@php
    use App\Http\Controllers\CompteController;
    $pages = CompteController::pagesAdmin();
@endphp
<header class="bg-black shadow p-2 flex justify-between items-center navbar">
    <div class="flex items-center gap-6">
        <img src="{{ asset('public/images/RomanaW.png') }}" alt="Logo" class="h-10 logo">
        <h1 class="text-5xl font-bold white"><a href="/">Romana</a></h1>
    </div>
    <div class="flex">
        <input type="checkbox" id="menu-toggle" />
        <label for="menu-toggle" id="burger">&#9776;</label>
        <div id="menu">
            @foreach ($pages as $page => $route)
            @if ($route)
                <nav class="route">
                    <a href="{{ $route }}" class="text-2xl">{{ $page }}</a>
                </nav>
            @else
                <nav>
                    <s class="text-2xl">{{ $page }}</s>
                </nav>
            @endif
            @endforeach
        </div>
    </div>
</header>