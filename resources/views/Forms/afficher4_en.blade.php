@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::modTZ();
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Cancellation</h3>

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

    @if (session('errors'))
        <div>
        @if (session('errors')->first('Success3'))
            <p class="green">Reservation successfully cancelled.</p>
        @elseif (session('errors')->first('NotFound3') || session('errors')->first('Cancelled3') ||
                session('errors')->first('TooLate3') || session('errors')->first('SQL3'))
            <p class="red">Error : Your reservation could not be cancelled...</p>
        @endif
        </div>
    @endif
</form>

@else

<p>
    <i>
        @if ((int)$reservation->statut_id == 6)
            @if (session('errors') && session('errors')->first('Success3'))
                <p class="green">Reservation successfully cancelled.</p>
            @else
                This reservation has already been cancelled.
            @endif
        @else
            You cannot cancel this reservation.
        @endif
    </i>
</p>

@endif