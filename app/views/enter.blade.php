@extends('layout')

@section('headline')
    Ein neues Spiel beginnen
@stop

@section('text')
    <p>Hier kannst Du eine neue Partei registrieren, mit der Du an Fantasya teilnimmst.</p>
    <p>Du kannst insgesamt 90 Ressourcen (Holz, Stein und Eisen) beliebig verteilen.</p>
    @include('enter-form')
@stop
