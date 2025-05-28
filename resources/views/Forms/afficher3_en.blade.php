@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::bornesTZ((int) $reservation->reservation_personnes)[0];
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Time slot</h3>

@push('scripts')
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            addDate("{{ substr($reservation->reservation_horaire, 0, 10) }}", true);
            addHour("{{ substr($reservation->reservation_horaire, 11)}}", true)
        });
    </script>
@endpush

@if ($early < $horaire && (int)$reservation->statut_id != 6)

&nbsp;
<form action="{{ route('api.modifhoraire') }}" method="POST">
    @csrf

    <input type="hidden" name="num" value="{{ $reservation->reservation_num}}">
    <input type="hidden" name="phone" value="{{ $reservation->reservation_telephone ?? ''}}">

    <label>Number of people
        <input type="number" name="amount" id="nbInput" min="1" required
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

    @if (session('errors'))
        <div>
        @if (session('errors')->first('Success2'))
            <p class="green">Reservation successfully modified.</p>
        @elseif (session('errors')->first('SlotTaken2'))
            <p class="red">The chosen time slot just expired!</p>
        @elseif (session('errors')->first('NotFound2') || session('errors')->first('Cancelled2') ||
                session('errors')->first('TooLate2') || session('errors')->first('SQL2'))
            <p class="red">Error : Your reservation could not be modified...</p>
        @endif
        </div>
    @endif
</form>

@else
    <label>Number of people
        <input type="number" name="amount" min="1" disabled
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