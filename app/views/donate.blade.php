@extends('layout')

@section('headline')
	Spenden
@stop

@section('text')
	<p>Der Betrieb des Servers für Fantasya kostet Geld.</p>
	<p>Es ist nicht viel, aber vielleicht gefällt Dir Fantasya so gut, dass Du gerne einen kleinen Beitrag zur Finanzierung leisten möchtest.</p>
	<p>Vielleicht kennst Du Bitcoins, die mittlerweile weit verbreitete virtuelle Währung des Internets, und möchtest ein paar mBTC für Fantasya spenden? Dann sende Deine Spende an diese Adresse:</p>
	<p><code>18GPpNuzLGJze9Hep8do8haTDfadjY3wtu</code></p>
	<p>Dankeschön!</p>
	<p style="text-align: center"><a href="bitcoin:18GPpNuzLGJze9Hep8do8haTDfadjY3wtu"><img src="/images/donate.png" /></a></p>
	@if (Auth::user() && Auth::user()->name === 'Thalian')
		<p><a href="https://blockchain.info/address/18GPpNuzLGJze9Hep8do8haTDfadjY3wtu" target="_blank">Spendenstatus in der Blockchain überprüfen</a></p>
	@endif
@stop
