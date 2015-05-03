@extends('layout')

@section('headline')
	Neuigkeiten
@stop

@section('text')
	<p>Seit Anfang 2015 ist Fantasya wieder online!</p>
	<p>Spielbefehle bitte an die E-Mail-Adresse <strong>befehle(at)fantasya-pbem.de</strong> senden. Für Fragen ist unser Forum, die Taverne, der geeignete Ort.</p>
	
	@foreach ($news as $article)
		<div class="article">
			<h3>{{Date::asDate($article->created_at)}} - {{{$article->title}}}</h3>
			<p>{{$article->content}}</p>
		</div>
	@endforeach
@stop
