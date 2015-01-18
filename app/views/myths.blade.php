@extends('layout')

@section('headline')
    Gerüchte
@stop

@section('text')
    @foreach ($myths as $myth)
        <p>{{$myth->myth}}</p>
    @endforeach
@stop
