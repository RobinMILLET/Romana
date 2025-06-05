@php
    use App\Http\Controllers\PlanningController;
    $early = PlanningController::bornesTZ((int) $reservation->reservation_personnes)[0];
    $horaire = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire);
@endphp

<h3>Modify the informations</h3>

@if ($early < $horaire && (int)$reservation->statut_id != 6)

&nbsp;
<form action="{{ route('api.modifinfo') }}" method="POST">
    @csrf

    <input type="hidden" name="num" value="{{ $reservation->reservation_num}}">
    <input type="hidden" name="phone" value="{{ $reservation->reservation_telephone ?? ''}}">

    <label>Last name
        <input type="text" name="lastname" maxlength="250" required
            value="{{ $reservation->reservation_nom ?? '' }}">
    </label>

    <label>First name
        <input type="text" name="firstname" maxlength="250" required
            value="{{ $reservation->reservation_prenom ?? '' }}">
    </label>

    <label>Other mentions
        <textarea name="other" maxlength="500"
            value="{{ $reservation->reservation_commentaire ?? '' }}"></textarea>
    </label>

    <button type="submit">Save</button>
</form>

@else
    <label>Last name
        <input type="text" name="lastname" maxlength="250" disabled
            value="{{ $reservation->reservation_nom ?? '' }}">
    </label>

    <label>First name
        <input type="text" name="firstname" maxlength="250" disabled
            value="{{ $reservation->reservation_prenom ?? '' }}">
    </label>

    <label>Other mentions
        <textarea name="other" maxlength="500" disabled
            value="{{ $reservation->reservation_commentaire ?? '' }}"></textarea>
    </label>

    <div>
        <p>
            You cannot modify this reservation.
        </p>
    </div>
@endif