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
            Benutzername: {{ Auth::user()->name }}<br>
            <em>
            @if (Auth::user()->flags == 0)
                Keine besonderen Berechtigungen.
            @else
                Besondere Berechtigungen:
                @if (User::has(User::CAN_CREATE_NEWS))
                    News verfassen
                @endif
            @endif
            </em>
        </p>
        <p>E-Mail-Adresse: {{ Auth::user()->email }}</p>
        @include('email-form')
        @include('password-form')
        @if ($saved)
            <p><strong>Änderung gespeichert.</strong></p>
        @endif
    @else
        <p>Das war wohl das falsche Passwort!</p>
        <p><a href="/reset">Neues Passwort anfordern</a></p>
    @endif
@stop
