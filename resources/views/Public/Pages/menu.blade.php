@extends('Public.Layouts.app')

@push('styles')
    @vite(['resources/css/menu.css'])
@endpush

@php
    use App\Models\Langue;
    use App\Http\Controllers\VitrineController;
    $active = session('locale', Langue::find(0));
@endphp

@section('content')

<section id="menu-nav">

@foreach ($menus as $category)

<a {{ $category->categorie_id == $id ? "class=active" : "" }} href="/menu/{{ $category->categorie_id }}">
    {{ $category->Traductible()->obtenirTraduction($active->langue_id)->traduction_libelle }}
</a>

@endforeach

</section>
<section id="menu-main">

@php
    VitrineController::menu($id);
@endphp

</section>

@endsection