@extends('Public.Layouts.app')

@push('styles')
    @vite(['resources/css/vitrine.css'])
@endpush

@push('scripts')
    @vite(['resources/js/Scripts/vitrine.js'])
    @if ($script)
        @vite(['resources/js/Scripts/reserver.js'])
    @endif
@endpush

@section('content')

@foreach ($lignes as $ligne)
    <section>
    @foreach ($ligne as $conteneur)
        <Conteneur :conteneur="{{$conteneur}}" :notext="{{!$textes[$conteneur->conteneur_id]?'true':'false'}}">
            @if (preg_match("/^Public\.Forms\.[a-z0-9]+(_[a-z]{2})?$/", $textes[$conteneur->conteneur_id]))
                @include($textes[$conteneur->conteneur_id])
            @else
                {!! $textes[$conteneur->conteneur_id] !!}
            @endif
        </Conteneur>
    @endforeach
    </section>
@endforeach

@endsection