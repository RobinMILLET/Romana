@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::modTZ();
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Annulation</h3>

<p>
    Vous avez changé d'avis ou ne pourrez pas être présent ?
    <br><br>
    Nous regrettons de vous voir partir, mais les imprévus arrivent, et nous comprennons.
    <br><br>
    Néanmoins, nous vous remercions d'avance, de la part des clients et du personnel,
    de signaler votre abscence à l'avance afin de faciliter notre travail.
</p>
<br><br>

@if ($early < $horaire && (int)$reservation->statut_id != 6)

<form action="{{ route('api.annulation') }}" method="POST">
    @csrf

    <input type="hidden" name="num" value="{{ $reservation->reservation_num}}">
    <input type="hidden" name="phone" value="{{ $reservation->reservation_telephone ?? ''}}">

    <button type="submit">Annuler ma réservation</button>

    @if (session('errors'))
        <div>
        @if (session('errors')->first('Success3'))
            <p class="green">La réservation à bien été annulée.</p>
        @elseif (session('errors')->first('NotFound3') || session('errors')->first('Cancelled3') ||
                session('errors')->first('TooLate3') || session('errors')->first('SQL3'))
            <p class="red">Erreur : Votre réservation n'a pas pu être modifiée...</p>
        @endif
        </div>
    @endif
</form>

@else

<p>
    <i>
        @if ((int)$reservation->statut_id == 6)
            @if (session('errors') && session('errors')->first('Success3'))
                <p class="green">La réservation à bien été annulée.</p>
            @else
                Cette réservation est déjà annulée.
            @endif
        @else
            Vous ne pouvez pas annuler cette réservation.
        @endif
    </i>
</p>

@endif