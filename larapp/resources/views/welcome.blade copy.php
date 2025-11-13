<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIMAS MTTG – Replica</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <link href="{{ asset('css/home.css') }}" rel="stylesheet"/>
</head>
<body>
  <div class="container">
<!-- LOGO DI KIRI -->
<!-- <div class="logo-left">
  <img src="https://svgshare.com/i/14jG.svg" alt="MTTG Logo" onerror="this.style.display='none'" />
  <span class="title">MTTG</span>
</div> -->

<!-- NAVBAR DI KANAN -->
<nav class="nav">
  <div class="nav-inner">
    <ul>
      <li><a class="active" href="#">Beranda</a></li>
      <li><a href="#">Masjid</a></li>
      <li><a href="#">Mushalla</a></li>
      <li><a href="#">Info Terkini</a></li>
      <li><a href="#">Unduh Data</a></li>
      <li><a href="#">Kontak Kami</a></li>
    </ul>
    <button class="login">Login</button>
  </div>
</nav>

    <!-- HERO -->
    <section class="hero">
      <center>
      <div class="hero-card">
        <div class="hero-media" aria-hidden="true"></div>
        <div class="hero-content">
          <h2>
            Sistem Informasi Majelis Taklim Telkom Group (MTTG)<br />
            <span class="accent">Majelis Taklim Telkom Group (MTTG)</span>
          </h2>
          <p class="subtitle">Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)</p>

          <div class="search-wrap">
            <div class="search">
              <input type="text" placeholder="cari data masjid / musholla" aria-label="Cari data" />
              <button class="btn-search" type="button" aria-label="Cari Data">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 2a8 8 0 105.293 14.293l4.207 4.207a1 1 0 001.414-1.414l-4.207-4.207A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z"/></svg>
                Cari Data
              </button>
            </div>
          </div>
        </div>
      </div>
      </center>
    </section>

    <!-- PRAYER TIMES -->
    <section class="times">
      <div class="section-title">Informasi / Jadwal Shalat</div>
      <div class="times-wrap">
        <div class="card"><div class="label">Subuh</div><div class="clock">03:43</div></div>
        <div class="card"><div class="label">Dzuhur</div><div class="clock">11:16</div></div>
        <div class="card"><div class="label">Ashar</div><div class="clock">14:31</div></div>
        <div class="card"><div class="label">Maghrib</div><div class="clock">17:27</div></div>
        <div class="card"><div class="label">Isya</div><div class="clock">18:39</div></div>
      </div>
    </section>

    <!-- SUMMARY CARDS -->
    <section class="summary">
      <div class="panel">
        <div class="header">
          <div>
            <div class="kicker">Ringkasan Informasi</div>
            <h2 class="title">Data Masjid dan Musholla</h2>
            <p class="desc">Di wilayah (Witel) Telkom Regional 3 Jatim, Bali, Nusa Tenggara</p>
          </div>
          <button class="btn-outline">Lihat Semua</button>
        </div>

        <div class="cards">
          <!-- Card: Jawa Timur -->
          <article class="summary-card">
            <div class="badge">Wilayah</div>
            <div class="region">Jawa Timur</div>

            <div class="meta-title">Berdasarkan Data Keseluruhan</div>
            <div class="stat">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg>
              <span><strong>Jumlah Masjid :</strong> 8</span>
            </div>
            <div class="stat">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg>
              <span><strong>Jumlah Musholla :</strong> 7</span>
            </div>
            <div class="stat">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z"/></svg>
              <span><strong>Jumlah BKM :</strong> 6</span>
            </div>

            <div class="meta-title">Berdasarkan Kelengkapan Fasilitas</div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Masjid :</strong> 6/8</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Musholla :</strong> 5/8</span></div>

            <button class="cta">Lihat Selengkapnya</button>
          </article>

          <!-- Card: Bali -->
          <article class="summary-card">
            <div class="badge">Wilayah</div>
            <div class="region">Bali</div>

            <div class="meta-title">Berdasarkan Data Keseluruhan</div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Jumlah Masjid :</strong> 3</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Jumlah Musholla :</strong> 4</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z"/></svg><span><strong>Jumlah BKM :</strong> 3</span></div>

            <div class="meta-title">Berdasarkan Kelengkapan Fasilitas</div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Masjid :</strong> 2/3</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Musholla :</strong> 3/4</span></div>

            <button class="cta">Lihat Selengkapnya</button>
          </article>

          <!-- Card: Nusa Tenggara -->
          <article class="summary-card">
            <div class="badge">Wilayah</div>
            <div class="region">Nusa Tenggara</div>

            <div class="meta-title">Berdasarkan Data Keseluruhan</div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Jumlah Masjid :</strong> 4</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Jumlah Musholla :</strong> 6</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z"/></svg><span><strong>Jumlah BKM :</strong> 5</span></div>

            <div class="meta-title">Berdasarkan Kelengkapan Fasilitas</div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Masjid :</strong> 4/6</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Musholla :</strong> 6/4</span></div>

            <button class="cta">Lihat Selengkapnya</button>
          </article>

          <!-- Card: Keseluruhan (dark) -->
          <article class="summary-card dark">
            <div class="badge">Wilayah</div>
            <div class="region">Keseluruhan</div>

            <div class="meta-title">Berdasarkan Data Keseluruhan</div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Jumlah Masjid :</strong> 15</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Jumlah Musholla :</strong> 17</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z"/></svg><span><strong>Jumlah BKM :</strong> 14</span></div>

            <div class="meta-title">Berdasarkan Kelengkapan Fasilitas</div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Masjid :</strong> 10/15</span></div>
            <div class="stat"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7h18v13H3zM7 3h10v2H7z"/></svg><span><strong>Musholla :</strong> 12/17</span></div>

            <button class="cta">Lihat Selengkapnya</button>
          </article>
        </div>
      </div>
    </section>
  </div>

    @include('index')

  <!-- Small helper: replace hero image quickly via data attribute -->
  <script>
    // Ganti URL gambar hero di bawah kalau perlu
    const HERO_URL = '';
    if (HERO_URL) {
      document.querySelector('.hero-media').style.setProperty('--hero', `url(${HERO_URL})`);
    }
  </script>
  </body>
</html>
