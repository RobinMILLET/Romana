@if (session()->has('success'))
    @if (session('success') == 'Success1')
        <p class="green">Your reservation was successfully saved.</p>
    @elseif (session('success') == 'Success2')
        <p class="green">The modified information has been saved.</p>
    @elseif (session('success') == 'Success3')
        <p class="green">The modified details has been saved.</p>
    @elseif (session('success') == 'Success4')
        <p class="green">The reservation was successfully cancelled.</p>
    @endif
@endif

@if (session()->has('errors'))
    @if (session('errors') == 'SlotTaken')
        <p class="red">The chosen time slot just expired!</p>
    @else
        <p class="red">Error : A technical error occured...</p>
    @endif
@endif

<h1>Your reservation N° <span class="code">{{ $reservation->reservation_num }}</span> at <span class="code">{{ $reservation->formattedPhone() }}</span></h1>
<p><i>Remember these informations to visualize, modify or cancel your reservation in the future.</i></p>