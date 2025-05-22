@php
  use App\Models\Constante;
@endphp

@extends('Layouts.app')

@push('styles')
    @vite(['resources/css/vitrine.css'])
@endpush

@push('scripts')
    @if (strval($page->page_id) == '6')
        @vite(['resources/js/Scripts/captcha.js'])
        @if (Constante::key('captcha_reservation'))
            <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
        @endif
        @vite(['resources/js/Scripts/reserver.js'])
    @endif
@endpush

@section('content')
{{ Constante::find('captcha_reservation')->valeur() }}
@foreach ($lignes as $ligne)
<section>
@foreach ($ligne as $conteneur)

<Conteneur :conteneur = "{{ $conteneur }}" >
    @if (preg_match("/^Forms\.[a-z]+_[a-z]{2}$/", $textes[$conteneur->conteneur_id]))
        @include($textes[$conteneur->conteneur_id])
    @else
        {!! $textes[$conteneur->conteneur_id] !!}
    @endif
</Conteneur>

@endforeach
</section>
@endforeach

@endsection