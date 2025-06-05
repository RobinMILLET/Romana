@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::modTZ();
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Annuler la réservation</h3>

<p>
    Vous avez changé d'avis ou ne pourrez pas être présent ?
    <br><br>
    Nous regrettons de vous voir partir, mais les imprévus arrivent, et nous comprennons.
    <br><br>
    Néanmoins, nous vous remercions d'avance, de la part des clients et du personnel,
    de signaler votre abscence au plus tôt afin de faciliter notre travail.
</p>
<br><br>

@if ($early < $horaire && (int)$reservation->statut_id != 6)

<form action="{{ route('api.annulation') }}" method="POST">
    @csrf

    <input type="hidden" name="num" value="{{ $reservation->reservation_num}}">
    <input type="hidden" name="phone" value="{{ $reservation->reservation_telephone ?? ''}}">

    <button type="submit">Annuler ma réservation</button>
</form>

@else

<p>
    <i>
        @if ((int)$reservation->statut_id == 6)
            Cette réservation est déjà annulée.
        @else
            Vous ne pouvez pas annuler cette réservation.
        @endif
    </i>
</p>

@endif