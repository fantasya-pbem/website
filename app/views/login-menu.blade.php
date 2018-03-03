<ul>
	<li>{{{Auth::user()->name}}} ist angemeldet.</li>
	<li><a href="/login">Profil</a></li>
	<li><a href="/change/world">Aktuelle Welt: {{{Game::current()->name}}}</a></li>
	@if (User::countParties(Game::current()) > 0)
		<li><a href="/orders">Befehle kontrollieren</a></li>
		<li><a href="/send/orders">Befehle senden</a></li>
		<li><a href="/report">Auswertung herunterladen</a></li>
	@endif
	@if (User::countAllParties(Game::current()) <= 0 || User::has(User::CAN_PLAY_MULTIS))
		<li><a href="/enter">Neues Spiel beginnen</a></li>
	@endif
	@if (User::has(User::CAN_BETA_TEST))
		<!-- <li class="beta"><a href=""></a></li> -->
	@endif
	@if (User::has(User::CAN_CREATE_NEWS))
		<li><a href="/edit/news">News erstellen</a></li>
	@endif
	<li><a href="/logout">Abmelden</a></li>
</ul>
