@extends('layout')

@section('headline')
    Befehle kontrollieren
@stop

@section('text')
    <h3>Welt: {{ Game::current()->name }}</h3>
    <p>Runde: {{ $turn }}</p>
    <p><pre>{{ $orders }}</pre></p>
@stop
