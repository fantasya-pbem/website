@extends('layout')

@section('headline')
    Ein neues Spiel beginnen
@stop

@section('text')
    <p>Hier kannst Du eine neue Partei registrieren, mit der Du an Fantasya teilnimmst.</p>
    <p>Du kannst insgesamt 90 Ressourcen (Holz, Stein und Eisen) beliebig verteilen.</p>
    <p>Beim nächsten ZAT wird Deine neue Partei in der Spielwelt ausgesetzt, und Du erhältst Deine erste Auswertung. Anschließend kannst Du die ersten Befehle abgeben.</p>
    @include('enter-form')
@stop
