<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta name="keywords" content="Fantasya PBEM Fantasy Rollenspiel" />
		<meta name="description" content="Fantasya ist eine Welt voll mit Elfen, Zwergen und anderen Lebewesen. Die Unterwelt ist überrannt worden von Drachen und Zombies, die darauf warten durch Höhlen zur Oberwelt zu gelangen. Fantasya ist ein kostenloses Runden-Strategiespiel, bei dem Du Deine eigene Zivilisation gründen und Dich mit anderen Spielern messen kannst." />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Fantasya</title>
		<link href="/style.css" rel="stylesheet" type="text/css" media="screen" />
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="logo">
					{{--
					<h1><a href="//www.fantasya-pbem.de">Fantasya</a></h1>
					--}}
				</div>
			</div>

            <div id="myth">
                <span>{{{Myth::getLast()}}}</span>
            </div>

			<div id="menu">
				<ul>
					<li><a href="/index">Fantasya</a></li>
					<li><a href="//wiki.fantasya-pbem.de">Bibliothek</a></li>
					<li><a href="//forum.fantasya-pbem.de">Taverne</a></li>
					{{-- <li class="last"><a href="#">Entwicklung</a></li> --}}
				</ul>
			</div>

			<div id="page">
				<div id="sidebar">
					<ul>
						<li>
							<h2>Fantasya</h2>
							<ul>
								<li><a href="/news">Neuigkeiten</a></li>
								<li><a href="/about">Über Fantasya</a></li>
								<li><a href="/myths">Gerüchte</a></li>
								{{--  <li><a href="#">Downloads</a></li> --}}
								<li><a href="/contact">Kontakt</a></li>
							</ul>
						</li>
						<li>
							<h2>Anmeldung</h2>
							@if (Auth::check())
							    <ul>
							        <li>{{ Auth::user()->name }} ist angemeldet.</li>
							        <li><a href="/login">Profil</a></li>
							        <li><a href="/logout">Abmelden</a></li>
							        <li>Weitere Aktionen werden bald verfügbar sein.</li>
							    </ul>
							@else
							     @include('login-form')
							@endif
						</li>
						<li>
						    <h2>Die Welten</h2>
						    <ul>
						        @foreach (Game::all() as $game)
                                    <li><a href="/world/{{{$game->id}}}">{{{$game->name}}}</a></li>
                                @endforeach
						    </ul>
						</li>
						<li>
							<h2>Links</h2>
							<ul>
								<li><a href="http://www.metadrei.org/toolbelt/forlage.html">Forlage</a></li>
								<li><a href="http://sourceforge.net/projects/magellan-client">Magellan-Client</a></li>
								<li><a href="http://www.gulrak.de">Vorlage-Tool</a></li>
								<li><a href="http://www.eressea.de">Eressea</a></li>
								<li><a href="http://www.german-atlantis.de">German Atlantis</a></li>
								<li><a href="http://www.pbem-spiele.de">PbEM-Spiele.de</a></li>
							</ul>
						</li>
					</ul>
				</div>

				<div id="content">
					<div class="post">
						<h2 class="title">@yield('headline')</h2>
						<div class="entry">@yield('text')</div>
					</div>
					</div>
					</div>
				<div style="clear: both;">&nbsp;</div>
				</div>

				<div style="clear: both;">&nbsp;</div>
			</div>
		</div>
		<div id="footer">
			<p>&copy; {{date('Y')}} fantasya.pbem. All rights reserved. Design by <a href="http://templated.co" rel="nofollow">TEMPLATED</a>, mogel &amp; Thalian.</p>
		</div>
	</body>
</html>
