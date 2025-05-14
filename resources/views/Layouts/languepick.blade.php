@php
    use App\Models\Langue;
    $langues = Langue::all();
    $active = session('locale', Langue::find(0));
@endphp

<form method="GET" action="#">
    <select onchange="window.location.href='/langue/' + this.value" class="rounded px-2 py-1 bg-black">
        @foreach ($langues as $langue)
            <option value="{{ $langue->langue_id}}" {{ $langue == $active ? 'selected' : '' }}>
                {{ $langue->langue_affichage }}
            </option>
        @endforeach
    </select>
</form>
