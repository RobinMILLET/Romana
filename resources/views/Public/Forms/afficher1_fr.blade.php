@if (session()->has('success'))
    @if (session('success') == 'Success1')
        <p class="green">Votre réservation à bien été prise en compte.</p>
    @elseif (session('success') == 'Success2')
        <p class="green">Les informations modifiées ont bien été prises en compte.</p>
    @elseif (session('success') == 'Success3')
        <p class="green">Les détails modifiés ont bien été pris en compte.</p>
    @elseif (session('success') == 'Success4')
        <p class="green">La réservation à bien été annulée.</p>
    @endif
@endif

@if (session()->has('errors'))
    @if (session('errors') == 'SlotTaken')
        <p class="red">Le créneau choisis viens d'expirer !</p>
    @else
        <p class="red">Erreur : Une erreur technique est survenue...</p>
    @endif
@endif

<h1>Votre réservation N° <span class="code">{{ $reservation->reservation_num }}</span> au <span class="code">{{ $reservation->formattedPhone() }}</span></h1>
<p><i>Conservez ces informations pour consulter, modifier ou annuler votre réservation dans le futur.</i></p>