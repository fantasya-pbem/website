@extends('layout')

@section('headline')
    Neuigkeiten
@stop

@section('text')
	<p>Seit Anfang 2015 ist Fantasya wieder online!</p>
	<p>Zur Zeit sind wir noch mit dem Wiederaufbau beschäftigt, daher funktioniert die Zugabgabe noch nicht so, wie ihr es von früher gewohnt seid. Wir halten Euch in der <em>Taverne</em>, unserem Forum, auf dem Laufenden, und senden E-Mail-Nachrichten an die alten Spieleradressen.</p>
	<p>Bitte richtet Eure Anfragen und Spielbefehle zunächst an die E-Mail-Adresse <strong>fantasya-host(at)web.de</strong>, oder fragt einfach in der Taverne nach.</p>
	
	@foreach ($news as $article)
	    <div class="article">
            <h3>{{ Date::asDate($article->created_at) }} - {{ $article->title }}</h3>
            <p>{{ $article->content }}</p>
        </div>
    @endforeach
@stop
