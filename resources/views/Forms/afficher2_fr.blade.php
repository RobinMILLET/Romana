@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::bornesTZ((int) $reservation->reservation_personnes)[0];
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Infomations</h3>

@if ($early < $horaire && (int)$reservation->statut_id != 6)

&nbsp;
<form action="{{ route('api.modifinfo') }}" method="POST">
    @csrf

    <input type="hidden" name="num" value="{{ $reservation->reservation_num}}">
    <input type="hidden" name="phone" value="{{ $reservation->reservation_telephone ?? ''}}">

    <label>Nom de famille
        <input type="text" name="lastname" maxlength="250" required
            value="{{ $reservation->reservation_nom ?? '' }}">
    </label>

    <label>Prénom
        <input type="text" name="firstname" maxlength="250" required
            value="{{ $reservation->reservation_prenom ?? '' }}">
    </label>

    <label>Autres mentions
        <textarea name="other" maxlength="500"
            value="{{ $reservation->reservation_commentaire ?? '' }}"></textarea>
    </label>

    <button type="submit">Enregistrer</button>

    @if (session('errors'))
        <div>
        @if (session('errors')->first('Success1'))
            <p class="green">Les modifications ont bien été prises en compte.</p>
        @elseif (session('errors')->first('NotFound1') || session('errors')->first('Cancelled1') ||
                session('errors')->first('TooLate1') || session('errors')->first('SQL1'))
            <p class="red">Erreur : Votre réservation n'a pas pu être modifiée...</p>
        @endif
        </div>
    @endif
</form>

@else
    <label>Nom de famille
        <input type="text" name="lastname" maxlength="250" disabled
            value="{{ $reservation->reservation_nom ?? '' }}">
    </label>

    <label>Prénom
        <input type="text" name="firstname" maxlength="250" disabled
            value="{{ $reservation->reservation_prenom ?? '' }}">
    </label>

    <label>Autres mentions
        <textarea name="other" maxlength="500" disabled
            value="{{ $reservation->reservation_commentaire ?? '' }}"></textarea>
    </label>

    <div>
        <p>
            Vous ne pouvez pas modifier les informations de cette réservation.
        </p>
    </div>
@endif