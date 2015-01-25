<ul>
    <li>{{ Auth::user()->name }} ist angemeldet.</li>
    <li><a href="/login">Profil</a></li>
    @if (User::has(User::CAN_CREATE_NEWS))
        <li><a href="/edit/news">News erstellen</a></li>
    @endif
    <li><a href="/logout">Abmelden</a></li>
    <li>Weitere Aktionen werden bald verfügbar sein.</li>
</ul>

