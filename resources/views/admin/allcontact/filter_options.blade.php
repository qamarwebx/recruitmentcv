@if(
$filter == 'business_type'
||
$filter == 'lead_priority'
)

@foreach($data as $item)

<option
value="{{ $item }}">

{{ $item }}

</option>

@endforeach

@elseif(
$filter == 'state'
||
$filter == 'lead_owner'
)

@foreach($data as $item)

@if(
$filter == 'state'
&&
$item->state
)

<option
value="{{ $item->state_id }}">

{{ $item->state->name }}

</option>

@endif


@if(
$filter == 'lead_owner'
&&
$item->owner
)

<option
value="{{ $item->owner_id }}">

{{ $item->owner->name }}

</option>

@endif

@endforeach

@else

@foreach($data as $item)

<option
value="{{ $item->id }}">

{{ $item->name }}

</option>

@endforeach

@endif