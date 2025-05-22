@php
  use App\Models\Constante;
@endphp

<form action="{{ route('api.book') }}" method="POST">
    <label>Nom de famille
        <input type="text" name="nom" maxlength="250" required>
    </label>

    <label>Prénom
        <input type="text" name="prenom" maxlength="250" required>
    </label>

    <label>Numéro de téléphone
        <input type="tel" name="telephone" pattern="((\+\d{1,2}[\-_\. ]?\d)|(0\d))([\-_\. ]?\d{2}){4}"
          required placeholder="01 23 45 67 89">
    </label>

    <label>Nombre de personnes
        <input type="number" name="personnes" id="nbInput" min="1" required>
    </label>

    <label>Date de la réservation
        <select name="date" id="dtInput" required>
            <option value="null">Sélectionnez une date</option>
        </select>
    </label>

    <label>Créneau horaire
        <select name="time" id="tmInput" required>
            <option value="null">Sélectionnez un créneau</option>
        </select>
    </label>

    <label>Autres mentions
        <textarea name="other" maxlength="500"></textarea>
    </label>

    @if (Constante::key('captcha_reservation'))
      <div class='g-recaptcha' data-sitekey='{{ env('RECAPTCHA_SITE_KEY')}}'
        data-theme='dark' data-callback='callback' data-expired-callback='expire'>
      </div>

      <button type="submit" disabled onmouseenter='submit_enter(this)' onmouseleave='submit_leave()'>Envoyer</button>
    @else
      <button type="submit">Envoyer</button>
    @endif
</form>