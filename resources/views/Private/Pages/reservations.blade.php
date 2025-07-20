@extends('Private.Layouts.app')

@push('styles')
    @vite(['resources/css/reservations.css'])
@endpush

@push('scripts')
    @vite(['resources/js/Scripts/reservations.js'])
@endpush

@section('content')

<details>
    <summary>⚙ Paramètres</summary>
    <section id="statuts">
        @foreach ($statuts as $statut)
        <div>
            <span style="background-color:#{{ $statut->statut_hex }}"></span>
            {{ $statut->statut_libelle }} ({{ $statut->nb }})
            <input type="checkbox" {{ $statut->statut_id == 1 ? 'checked' : '' }}
                onchange="statut(this, {{ $statut->statut_id }})">
        </div>
        @endforeach
    </section>
    <section id="params">
        <div>
            Afficher les infos personelles
            <input type="checkbox" onchange="mask(this.checked)">
        </div>
        @if ($diff > 0)
        <div>
            <a href="{{ route('admin.reservations.more') }}">
            <button>Charger ({{ $diff }}) réservations en plus</button>
            </a>
        </div>
        @endif
    </section>
</details>

<Reservation :reservation="{
    'reservation_num':'Numéro',
    'reservation_nom':'Nom',
    'reservation_prenom':'Prénom',
    'reservation_personnes':'Pl.',
    'reservation_commentaire':'Commentaire',
    'reservation_creation':null,
    'reservation_horaire':null
}"  :statut="{
    'statut_libelle':'Statut',
    'statut_hex':'00000000'
}"  :telephone="'Téléphone'"
    :personnel="''"
id="example">
</Reservation>
<section id="reservations">
@foreach ($reservations as $reservation)

<Reservation
    :reservation="{{ $reservation }}"
    :statut="{{ $reservation->Statut() }}"
    :telephone="'{{ $reservation->formattedPhone() }}'"
    :personnel="'{{ $reservation->Personnel()?->personnel_nom }}'">
</Reservation>

@endforeach
</section>

@endsection