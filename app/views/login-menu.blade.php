<ul>
    <li>{{ Auth::user()->name }} ist angemeldet.</li>
    <li><a href="/login">Profil</a></li>
    @if (Game::current())
        <li><a href="/change/world">Aktuelle Welt: {{ Game::current()->name }}</a></li>
        <li><a href="/orders">Befehle kontrollieren</a></li>
        <li><a href="/send/orders">Befehle senden</a></li>
    @else
        @if (User::has(User::CAN_BETA_TEST))
            <li class="beta"><a href="/new/game">Spiel beginnen</a></li>
        @else
            <li>Weitere Aktionen werden bald verfügbar sein.</li>
        @endif
    @endif
    @if (User::has(User::CAN_CREATE_NEWS))
        <li><a href="/edit/news">News erstellen</a></li>
    @endif
    <li><a href="/logout">Abmelden</a></li>
</ul>
