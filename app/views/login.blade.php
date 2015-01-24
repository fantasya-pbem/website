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
        <p>E-Mail-Adresse: {{ Auth::user()->email }}</p>
    @else
        <p>Das war wohl das falsche Passwort!</p>
        <p><a href="/reset">Neues Passwort anfordern</a></p>
    @endif
@stop
