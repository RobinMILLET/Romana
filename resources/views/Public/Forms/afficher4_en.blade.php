@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::modTZ();
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Cancel reservation</h3>

<p>
    You changed your mind or won't be able to honor you time slot?
    <br><br>
    We regret to see you go, but life happens and we understand.
    <br><br>
    However, we thank you in advance, on behalf of all clients and staff,
    for signaling your ascence as soon as possible to make our service easier.
</p>
<br><br>

@if ($early < $horaire && (int)$reservation->statut_id != 6)

<form action="{{ route('api.annulation') }}" method="POST">
    @csrf

    <input type="hidden" name="num" value="{{ $reservation->reservation_num}}">
    <input type="hidden" name="phone" value="{{ $reservation->reservation_telephone ?? ''}}">

    <button type="submit">Cancel my reservation</button>
</form>

@else

<p>
    <i>
        @if ((int)$reservation->statut_id == 6)
            This reservation has already been cancelled.
        @else
            You cannot cancel this reservation.
        @endif
    </i>
</p>

@endif