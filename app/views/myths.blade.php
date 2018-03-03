@extends('layout')

@section('headline')
	Gerüchte
@stop

@section('text')
	@if (User::canCreateMyths())
		<p style="float: right;"><a href="/myth">» ein Gerücht in die Welt setzen</a></p>
		<p style="clear: right;"></p>
	@endif
	@foreach ($myths as $myth)
		<p>{{{$myth->myth}}}</p>
	@endforeach
@stop
