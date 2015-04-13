@extends('layout')

@section('headline')
	Befehle senden
@stop

@section('text')
	<h3>Welt: {{{Game::current()->name}}}</h3>
	<p>@include('send-orders-form')</p>
@stop
