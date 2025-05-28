@php
    use App\Http\Controllers\AppController;
    $pages = AppController::obtenirPagesTraduites()
@endphp
<header class="bg-black shadow p-2 flex justify-between items-center navbar">
    <div class="flex items-center gap-4">
        <img src="{{ asset('public/images/RomanaW.png') }}" alt="Logo" class="h-10 logo">
        <h1 class="text-5xl font-bold white"><a href="/">Romana</a></h1>
    </div>
    <div id="nav" class="bg-black shadow p-3 flex gap-4">
        @foreach ($pages as $page)
            <nav class="space-x-4">
                <a href="{{ route($page->page_route) }}" class="text-2xl">{{ $page->page_traduction_libelle }}</a>
            </nav>
        @endforeach
    </div>
    <div class="flex">
        <input type="checkbox" id="menu-toggle" />
        <label for="menu-toggle" id="burger">&#9776;</label>
        @include('Layouts.languepick')
        <div id="menu">
            @foreach ($pages as $page)
                <nav>
                    <a href="{{ route($page->page_route) }}" class="text-2xl">{{ $page->page_traduction_libelle }}</a>
                </nav>
            @endforeach
        </div>
    </div>
</header>