@extends('layout')

@section('headline')
    Befehle kontrollieren
@stop

@section('text')
    <h3>Welt: {{Game::current()->name}}</h3>
    <p>@include('orders-form')</p>
    <p>{{$party->name}}, Runde {{$turn + 1}}</p>
    <p><pre>{{$orders}}</pre></p>
@stop
