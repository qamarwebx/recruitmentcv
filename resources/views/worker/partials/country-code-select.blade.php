{{-- Phone country-code <select> shared by the Partner Login/Register modal
and the customer account Change Mobile modal (App\Support\CountryCodeOptions).
$attr: the data attribute the page's JS looks it up by. $selected: code to
preselect (optional). $searchable: true = country-code-select.js gives it the
same Select2 upgrade partner-auth.js gives the Partner Login select (needs
jQuery + Select2 on the page; otherwise it stays a native select). --}}
<select class="w-select" {{ $attr }} data-country-code-select @if (!empty($searchable)) data-country-searchable @endif>
    @foreach (\App\Support\CountryCodeOptions::all() as $code => $option)
        <option value="{{ $code }}" @selected(isset($selected) && (string) $selected === (string) $code)>{{ $option['flag'] }} {{ $option['label'] }} +{{ $code }}</option>
    @endforeach
</select>
