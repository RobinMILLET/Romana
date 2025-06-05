@php
  use App\Models\Constante;
@endphp

<h3>Enregistrer une réservation</h3>
&nbsp;
<form action="{{ route('api.book') }}" method="POST">
    @csrf
    <label>Nom de famille
        <input type="text" name="lastname" maxlength="250" required>
    </label>

    <label>Prénom
        <input type="text" name="firstname" maxlength="250" required>
    </label>

    <label>Numéro de téléphone
        <input type="tel" name="phone" required placeholder="01 23 45 67 89"
          pattern="((\+\d{1,2}[\-_\. ]?[1-9])|(0[1-9]))([\-_\. ]?\d{2}){4}" maxlength="17">
    </label>

    <label>Nombre de personnes
        <input type="number" name="amount" id="nbInput" required
            min="1" value="1" max="{{ Constante::key('réservation_personnes_max') }}">
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

    <label>Autres mentions
        <textarea name="other" maxlength="500"></textarea>
    </label>

    @if (Constante::key('captcha_réservation'))
      <div class='g-recaptcha' data-sitekey='{{ env('RECAPTCHA_SITE_KEY')}}'
        data-theme='dark' data-callback='callback' data-expired-callback='expire'>
      </div>

      <button type="submit" disabled onmouseenter='submit_enter(this)' onmouseleave='submit_leave()'>Envoyer</button>
    @else
      <button type="submit">Valider</button>
    @endif

    @if (session('errors'))
        @if (session('errors')->first('SlotTaken'))
            <p class="red">Le créneau choisis viens d'expirer !</p>
        @elseif (session('errors')->first('SMS'))
            <p class="red">Erreur : Nous n'avons pas pu vérifier votre numéro de téléphone.</p>
        @elseif (session('errors')->first('SQL'))
            <p class="red">Erreur : Votre réservation n'a pas pu être créée...</p>
        @endif
    @endif
</form>