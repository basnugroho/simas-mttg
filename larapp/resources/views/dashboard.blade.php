<x-admin.layout title="MTTG - Dashboard">
  @auth
    <div class="logo-left d-flex align-items-center gap-2 shadow-sm">
      <img src="https://svgshare.com/i/14jG.svg" alt="Logo MTTG" onerror="this.style.display='none'">
      <span class="title">MTTG</span>
    </div>

    <!-- NAVBAR (dengan hamburger untuk mobile) -->
    <nav class="nav" role="navigation" aria-label="Menu utama">
      <div class="nav-inner">
        <!-- Tombol hamburger: tampil hanya di mobile -->
        <button class="hamburger" type="button" aria-label="Buka menu" aria-controls="primary-menu" aria-expanded="false">
          <!-- menu icon -->
          <svg class="icon-menu" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 6h18a1 1 0 0 0 0-2H3a1 1 0 1 0 0 2zm18 5H3a1 1 0 1 0 0 2h18a1 1 0 1 0 0-2zm0 7H3a1 1 0 1 0 0 2h18a1 1 0 1 0 0-2z"/></svg>
          <!-- close icon -->
          <svg class="icon-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.225 4.811a1 1 0 0 0-1.414 1.414L10.586 12l-5.775 5.775a1 1 0 1 0 1.414 1.414L12 13.414l5.775 5.775a1 1 0 0 0 1.414-1.414L13.414 12l5.775-5.775a1 1 0 1 0-1.414-1.414L12 10.586 6.225 4.811z"/></svg>
        </button>

        <ul id="primary-menu" class="d-flex align-items-center">
          <li><a class="active" href="#">Beranda</a></li>
          <li><a href="#">Masjid</a></li>
          <li><a href="#">Mushalla</a></li>
          <li><a href="#">Info Terkini</a></li>
          <li><a href="#">Unduh Data</a></li>
          <li><a href="#">Kontak Kami</a></li>
        </ul>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="login" type="submit">Logout</button></form>
      </div>
    </nav>

    <main class="admin-content container">
      <!-- Hero -->
      <section class="hero d-flex justify-content-center">
        <div class="hero-card">
          <div class="hero-media" aria-hidden="true"></div>
          <div class="hero-content text-center">
            <h2 class="mb-2 fw-bold">
              Sistem Informasi Majelis Taklim Telkom Group (MTTG)<br />
              <span class="accent">Majelis Taklim Telkom Group (MTTG)</span>
            </h2>
            <p class="subtitle mb-0">Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)</p>
          </div>
        </div>
      </section>

      <!-- Place admin widgets below -->
      <div class="mt-4">
        <h3>Selamat datang, {{ Auth::user()->name ?? 'Admin' }}</h3>
        <p class="text-muted">Gunakan panel ini untuk mengelola data.</p>
      </div>
    </main>
  @else
    <div class="container py-6">
      <div class="card p-4">
        <h4>Autentikasi Diperlukan</h4>
        <p>Anda perlu <a href="{{ route('login') }}">login</a> untuk mengakses dashboard.</p>
      </div>
    </div>
  @endauth
</x-admin.layout>
