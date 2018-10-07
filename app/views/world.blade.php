@extends('layout')

@section('headline')
	Die Welten
@stop

@section('text')
	<div id="world">
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
			<p><b>Achtung:</b> Im Beta-Spiel gibt es derzeit nur an manchen Wochentagen eine Auswertung, Details siehe Taverne.</p>
		@endif

		@if ($newCount > 0)
			<h3>Neue Parteien</h3>
			<p>
				@if ($newCount === 1)
					Diese Woche startet {{{$newCount}}} neue Partei.
				@else
					Diese Woche starten {{{$newCount}}} neue Parteien.
				@endif
			</p>
			<dl class="parties">
				@foreach ($newParties as $party)
					<dt>{{{$party->name}}}</dt>
					<dd>{{{$party->description}}}</dd>
				@endforeach
			</dl>
		@endif

		<h3>Statistik</h3>
		<h4>Regionen</h4>
		<p>
			Es gibt {{{$layers[1]->count}}} Regionen auf dieser Spielwelt und {{{$layers[0]->count}}} Regionen in der Unterwelt.
		</p>
		<table class="statistic">
			<thead>
				<tr>
					@foreach ($regions as $region)
						<th>{{{$region->typ}}}</th>
					@endforeach
				<tr>
			</thead>
			<tbody>
				<tr>
					@foreach ($regions as $region)
						<td>{{{$region->count}}}</td>
					@endforeach
				<tr>
			</tbody>
		</table>
		<table class="statistic">
			<thead>
				<tr>
					@foreach ($underworld as $region)
						<th>{{{$region->typ}}}</th>
					@endforeach
				<tr>
			</thead>
			<tbody>
				<tr>
					@foreach ($underworld as $region)
						<td>{{{$region->count}}}</td>
					@endforeach
				<tr>
			</tbody>
		</table>
		<h4>Bevölkerung</h4>
		<p>
			Es gibt insgesamt {{{$total[0]->units}}} Einheiten mit {{{$total[0]->persons}}} Individuen (Spieler und Monster).
		</p>
		<table class="statistic">
			<thead>
				<tr>
					@foreach ($units as $unit)
						<th>{{{$unit->rasse}}}</th>
					@endforeach
				<tr>
			</thead>
			<tbody>
				<tr>
					@foreach ($units as $unit)
						<td>{{{$unit->units}}}/{{{$unit->persons}}}</td>
					@endforeach
				<tr>
			</tbody>
		</table>
		<table class="statistic">
			<thead>
				<tr>
					@foreach ($monsters as $unit)
						<th>{{{$unit->rasse}}}</th>
					@endforeach
				<tr>
			</thead>
			<tbody>
				<tr>
					@foreach ($monsters as $unit)
						<td>{{{$unit->units}}}/{{{$unit->persons}}}</td>
					@endforeach
				<tr>
			</tbody>
		</table>
		<h4>Parteien</h4>
		<p>
			Diese Spielwelt wird von {{{$count}}} Parteien bevölkert.
		</p>
		<table class="statistic">
			<thead>
				<tr>
					@foreach ($parties as $group)
						<th>{{{$group->rasse}}}</th>
					@endforeach
				<tr>
			</thead>
			<tbody>
				<tr>
					@foreach ($parties as $group)
						<td>{{{$group->count}}}</td>
					@endforeach
				<tr>
			</tbody>
		</table>
		<dl class="parties">
		@foreach ($names as $party)
			<dt>{{{$party->name}}}</dt>
			<dd>{{{$party->beschreibung}}}</dd>
		@endforeach
		</dl>
	</div>
@stop
