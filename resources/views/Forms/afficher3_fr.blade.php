@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::bornesTZ((int) $reservation->reservation_personnes)[0];
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Horaire</h3>

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

    @if (session('errors'))
        <div>
        @if (session('errors')->first('Success2'))
            <p class="green">Les modifications ont bien été prises en compte.</p>
        @elseif (session('errors')->first('SlotTaken2'))
            <p class="red">Le créneau choisis viens d'expirer !</p>
        @elseif (session('errors')->first('NotFound2') || session('errors')->first('Cancelled2') ||
                session('errors')->first('TooLate2') || session('errors')->first('SQL2'))
            <p class="red">Erreur : Votre réservation n'a pas pu être modifiée...</p>
        @endif
        </div>
    @endif
</form>

@else
    <label>Nombre de personnes
        <input type="number" name="amount" min="1" disabled
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