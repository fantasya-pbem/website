@extends('layout')

@section('headline')
	Neues Passwort anfordern
@stop

@section('text')
	@if ($success === null)
		@include('reset-form')
	@elseif ($success === true)
		<p>Eine E-Mail mit dem neuen Passwort wurde versendet.</p>
	@else
		<p>Benutzer nicht gefunden.</p>
		<p><a href="/reset">Neuer Versuch</a></p>
	@endif
@stop
