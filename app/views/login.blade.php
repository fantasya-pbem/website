@extends('layout')

@section('headline')
	@if (Auth::check())
		Mein Fantasya-Profil
	@else
		Anmeldung fehlgeschlagen
	@endif
@stop

@section('text')
	@if (Auth::check())
		<p>
			Benutzername: {{{Auth::user()->name}}}<br>
			<em>
			@if (empty($flags))
				Keine besonderen Berechtigungen.
			@else
				Besondere Berechtigungen: {{implode(', ', $flags)}}
			@endif
			</em>
		</p>
		<p>E-Mail-Adresse: {{{Auth::user()->email}}}</p>
		@include('email-form')
		@include('password-form')
		<p>Das neue Passwort gilt für die Anmeldung und die Befehlsabgabe per E-Mail.</p>
		@if ($saved)
			<p><strong>Änderung gespeichert.</strong></p>
		@endif

		@foreach ($parties as $world => $partiesInWorld)
			<h3>Parteien auf Welt {{{$games[$world]->name}}}</h3>
			@if (count($partiesInWorld) === 0)
				<p>Keine Parteien angemeldet.</p>
			@else
				@foreach ($partiesInWorld as $party)
					<h4>{{{$party->name}}} [{{{$party->id}}}]</h4>
					<p>{{{$party->rasse}}}</p>
					<p>{{{$party->beschreibung}}}</p>
				@endforeach
			@endif
		@endforeach
	@else
		<p>Das war wohl das falsche Passwort!</p>
		<p><a href="/reset">Neues Passwort anfordern</a></p>
	@endif
@stop
