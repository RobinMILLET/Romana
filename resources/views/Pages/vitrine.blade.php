@extends('Layouts.app')
@vite(['resources/css/vitrine.css'])

@if (strval($page->page_id) == '6')
    @vite(['resources/js/Scripts/captcha.js'])
@endif

@section('content')

@foreach ($lignes as $ligne)
<section>
@foreach ($ligne as $conteneur)

<Conteneur :conteneur = "{{ $conteneur }}" >
{!! $textes[$conteneur->conteneur_id] !!}
</Conteneur>

@endforeach
</section>
@endforeach

@endsection