@php
    use App\Http\Controllers\PlanningController;
    use App\Models\Constante;
    $early = PlanningController::bornesTZ((int) $reservation->reservation_personnes)[0];
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Modifier les détails</h3>

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

    <label>Nombre de personnes
        <input type="number" name="amount" id="nbInput" min="1" required
            max="{{ Constante::key('réservation_personnes_max') }}"
            value="{{ $reservation->reservation_personnes}}">
    </label>

    <label>Date de la réservation
        <select name="date" id="dtInput" required>
            <option value="">Sélectionnez une date</option>
        </select>
    </label>

    <label>Créneau horaire
        <select name="time" id="tmInput" required>
            <option value="">Sélectionnez un créneau</option>
        </select>
    </label>

    <button type="submit">Enregistrer</button>
</form>

@else
    <label>Nombre de personnes
        <input type="number" name="amount" min="1" disabled
            max="{{ Constante::key('réservation_personnes_max') }}"
            value="{{ $reservation->reservation_personnes}}">
    </label>

    <label>Date de la réservation
        <select name="date" id="dtInput" disabled>
        </select>
    </label>

    <label>Créneau horaire
        <select name="time" id="tmInput" disabled>
        </select>
    </label>

    <div>
        <p>
            Vous ne pouvez pas modifier l'horaire de cette réservation.
        </p>
    </div>
@endif