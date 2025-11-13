<nav class="nav" role="navigation" aria-label="Menu utama">
  <div class="nav-inner">
    <ul>
      <li><a class="active" href="#">Beranda</a></li>
      <li><a href="#">Masjid</a></li>
      <li><a href="#">Mushalla</a></li>
      <li><a href="#">Info Terkini</a></li>
      <li><a href="#">Unduh Data</a></li>
      <li><a href="#">Kontak Kami</a></li>
    </ul>
    @auth
      <form method="POST" action="{{ route('logout') }}" style="margin:0">
        @csrf
        <button type="submit" class="login">Logout</button>
      </form>
    @else
      <a href="{{ route('login') }}" class="login" role="button">Login</a>
    @endauth
  </div>
</nav>