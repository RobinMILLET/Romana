@php
    use App\Http\Controllers\AppController;
    $pages = AppController::obtenirPagesTraduites()
@endphp
<header class="bg-black shadow p-3 flex justify-between items-center navbar">
    <div class="flex items-center gap-4">
        <img src="{{ asset('public/images/RomanaW.png') }}" alt="Logo" class="h-10 logo">
        <h1 class="text-5xl font-bold white">Romana</h1>
    </div>
    <div class="bg-black shadow p-3 flex justify-between items-center navbar gap-4">
        @foreach ($pages as $page)
            <nav class="space-x-4">
                <a href="{{ route($page->page_route) }}" class="text-2xl">{{ $page->page_traduction_libelle }}</a>
            </nav>
        @endforeach
    </div>
    @include('Layouts.languepick')
</header>