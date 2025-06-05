@php
  use App\Models\Constante;
@endphp

<h3>Book a reservation</h3>
&nbsp;
<form action="{{ route('api.book') }}" method="POST">
    @csrf
    <label>Last name
        <input type="text" name="lastname" maxlength="250" required>
    </label>

    <label>First name
        <input type="text" name="firstname" maxlength="250" required>
    </label>

    <label>Phone number
        <input type="tel" name="phone" required placeholder="01 23 45 67 89"
          pattern="((\+\d{1,2}[\-_\. ]?[1-9])|(0[1-9]))([\-_\. ]?\d{2}){4}" maxlength="17">
    </label>

    <label>Number of people
        <input type="number" name="amount" id="nbInput" required
            min="1" value="1" max="{{ Constante::key('réservation_personnes_max') }}">
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

    <label>Other mentions
        <textarea name="other" maxlength="500"></textarea>
    </label>

    @if (Constante::key('captcha_réservation'))
      <div class='g-recaptcha' data-sitekey='{{ env('RECAPTCHA_SITE_KEY')}}'
        data-theme='dark' data-callback='callback' data-expired-callback='expire'>
      </div>

      <button type="submit" disabled onmouseenter='submit_enter(this)' onmouseleave='submit_leave()'>Send</button>
    @else
      <button type="submit">Confirm</button>
    @endif

    @if (session('errors'))
        @if (session('errors')->first('SlotTaken'))
            <p class="red">The chosen time slot just expired!</p>
        @elseif (session('errors')->first('SMS'))
            <p class="red">Could not verify your phone number.</p>
        @elseif (session('errors')->first('SQL'))
            <p class="red">Error : Your reservation coul not be created...</p>
        @endif
    @endif
</form>