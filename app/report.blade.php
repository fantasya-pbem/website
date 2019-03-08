@extends('layout')

@section('headline')
    Auswertung herunterladen
@stop

@section('text')
    @foreach ($parties as $id => $name)
        <h3>{{{$name}}}</h3>
        @if (empty($turns[$id]))
            <p>Keine Auswertungen vorhanden.</p>
        @else
            <p>@include('report-form')</p>
        @endif
    @endforeach
@stop
