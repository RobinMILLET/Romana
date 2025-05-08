@extends('Layouts.app')
@vite(['resources/css/vitrine.css'])

@section('content')

@foreach ($lignes as $ligne)
<section>
@foreach ($ligne as $conteneur)

<Conteneur :conteneur = "{{ $conteneur }}" >
{!! $conteneur->conteneur_contenu !!}
</Conteneur>

@endforeach
</section>
@endforeach

@endsection