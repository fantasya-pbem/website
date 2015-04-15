@extends('layout')

@section('headline')
	Die Welten
@stop

@section('text')
	<h3>{{{$game->name}}}</h3>
	<p>{{$game->description}}</p>
	<p>
		<strong>Auswertung: </strong>
		@if ($game->adddays == 1)
			täglich,
		@else
			{{Weekday::translate($game->adddays)}},
		@endif
		{{{$game->addhours}}} Uhr
	</p>
	<p>
		<strong>Letzter Zug: </strong>Runde {{{$turn}}} vom {{Date::translate($lastZAT)}}<br>
		<strong>Nächster Zug: </strong>Runde {{$turn + 1}} am {{Date::translate(Date::getNext($game))}}
	</p>
	@if ($game->alias === 'beta')
		<p><b>Achtung:</b> Im Beta-Spiel gibt es zur Zeit keine täglichen Auswertungen.</p>
	@endif
@stop
