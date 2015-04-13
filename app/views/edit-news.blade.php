@extends('layout')

@section('headline')
	Neuigkeiten bearbeiten
@stop

@section('text')
	<h3>News erstellen</h3>
	@include('edit-news-form')

	<h3 id="list">News bearbeiten</h3>
	@if (empty($news))
		Keine Neuigkeiten vorhanden.
	@endif
	@foreach ($news as $article)
		<div class="article">
			<h3>{{Date::asDate($article->created_at)}} - {{{$article->title}}}</h3>
			<p>
				{{$article->content}}
			</p>
			<p class="edit">
				<a href="/delete/news/{{{$article->id}}}">Löschen</a>
			</p>
		</div>
	@endforeach
@stop
