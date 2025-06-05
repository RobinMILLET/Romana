@php
  use App\Models\Constante;
@endphp

<h3>Find a reservation</h3>
&nbsp;

<form action="{{ route('api.find') }}" method="POST">
    @csrf

    <label>Phone number
        <input type="tel" name="phone" required placeholder="01 23 45 67 89"
          pattern="((\+\d{1,2}[\-_\. ]?[1-9])|(0[1-9]))([\-_\. ]?\d{2}){4}" maxlength="17">
    </label>

    <label>Reservation number
        <input type="text" name="num" required placeholder="A1B2C3D4"
          pattern="[0-9A-Za-z]{8}" maxlength="8">
    </label>

    @if (Constante::key('captcha_consultation'))
      <div class='g-recaptcha' data-sitekey='{{ env('RECAPTCHA_SITE_KEY')}}'
        data-theme='dark' data-callback='callback' data-expired-callback='expire'>
      </div>

      <button type="submit" disabled onmouseenter='submit_enter(this)' onmouseleave='submit_leave()'>Send</button>
    @else
      <button type="submit">Confirm</button>
    @endif

    @if (session('errors'))
        @if (session('errors')->first('NotFound'))
            <p class="red">Could not find the corresponding reservation.</p>
        @endif
    @endif
</form>