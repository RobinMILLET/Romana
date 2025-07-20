@php
    use App\Http\Controllers\PlanningController;
    use App\Http\Controllers\ReservationController;
    use App\Models\Constante;
    $early = PlanningController::bornesTZ((int) $reservation->reservation_personnes)[0];
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Modify the details</h3>

@push('scripts')
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            addDate("{{ substr($reservation->reservation_horaire, 0, 10) }}", true);
            addHour("{{ substr($reservation->reservation_horaire, 11)}}", true)
        });
    </script>
@endpush

@if ($early < $horaire && (int)$reservation->statut_id != ReservationController::$CANCELLED_ID)

&nbsp;
<form action="{{ route('api.modifhoraire') }}" method="POST">
    @csrf

    <input type="hidden" name="num" value="{{ $reservation->reservation_num}}">
    <input type="hidden" name="phone" value="{{ $reservation->reservation_telephone ?? ''}}">

    <label>Number of people
        <input type="number" name="amount" id="nbInput" min="1" required
            max="{{ Constante::key('réservation_personnes_max') }}"
            value="{{ $reservation->reservation_personnes}}">
    </label>

    <label>Date of the reservation
        <select name="date" id="dtInput" required>
            <option value="">Select a date</option>
        </select>
    </label>

    <label>Time slot
        <select name="time" id="tmInput" required>
            <option value="">Select a time slot</option>
        </select>
    </label>

    <button type="submit">Save</button>
</form>

@else
    <label>Number of people
        <input type="number" name="amount" min="1" disabled
            max="{{ Constante::key('réservation_personnes_max') }}"
            value="{{ $reservation->reservation_personnes}}">
    </label>

    <label>Date of the reservation
        <select name="date" id="dtInput" disabled>
        </select>
    </label>

    <label>Time slot
        <select name="time" id="tmInput" disabled>
        </select>
    </label>

    <div>
        <p>
            You cannot modify this reservation.
        </p>
    </div>
@endif