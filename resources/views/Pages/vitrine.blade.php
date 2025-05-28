@extends($public ? 'Layouts.public' : 'Layouts.private')

@push('styles')
    @vite(['resources/css/vitrine.css'])
@endpush

@push('scripts')
    @if ($script)
        @if ($captcha)
            @vite(['resources/js/Scripts/captcha.js'])
            <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
        @endif
        @vite(['resources/js/Scripts/reserver.js'])
    @endif
@endpush

@section('content')
@foreach ($lignes as $ligne)
<section>
@foreach ($ligne as $conteneur)

<Conteneur :conteneur = "{{ $conteneur }}" >
    @if (preg_match("/^Forms\.[a-z0-9]+_[a-z]{2}$/", $textes[$conteneur->conteneur_id]))
        @include($textes[$conteneur->conteneur_id])
    @else
        {!! $textes[$conteneur->conteneur_id] !!}
    @endif
</Conteneur>

@endforeach
</section>
@endforeach

@endsection