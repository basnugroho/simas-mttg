<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>MTTG</title>

  <!-- Font Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body{
      background:#f5f5f7;
      font-family: 'Inter', sans-serif !important;
    }

    /* NAVBAR PILL */
    .nav-pill-wrapper{
      max-width:800px;
      margin:10px auto 0;
      justify-content:right;
    }
    .navbar-rounded{
      border-radius:50px;
      box-shadow:0 15px 35px rgba(0,0,0,.08);
      padding-inline:1.5rem;
    }
    .btn-login{
      border-radius:999px;
      padding:.45rem 1.4rem;
      box-shadow:0 8px 20px rgba(239,68,68,.35);
      font-weight:600;
    }

    /* HERO CARD (versi kamu) */
    .hero-section{
      margin-top:30px;
    }

    .hero-card{
      position:relative;
      overflow:hidden;
      border-radius:22px;
      min-height:420px;         /* sesuai request */
      background:#0b1020;       /* sesuai request */
      box-shadow:0 25px 55px rgba(0,0,0,.25);
      padding:3rem 2rem;
    }

    .hero-card .hero-bg{
      position:absolute;
      inset:0;
      background:url("{{ asset('images/mosque.png') }}") center/cover no-repeat;
      opacity:.25; /* sedikit transparan */
      z-index:0;
    }

    .hero-content{
      position:relative;
      z-index:2;
      color:#fff;
      max-width:900px;
      margin:auto;
      text-align:center;
    }

  
    .accent{
      color:#fecaca;
    }

    /* SEARCH */
    .search-wrapper{
      max-width:650px;
      margin:auto;
      margin-top:20px;
    }
    .search-wrapper .form-control{
      border-radius:999px 0 0 999px;
      padding:.9rem 1.2rem;
      border:none;
      font-size:.95rem;
      font-weight:400;
    }
    .search-wrapper .btn-search{
      border-radius:0 999px 999px 0;
      padding:.9rem 1.4rem;
      font-weight:600;
      box-shadow:0 10px 25px rgba(239,68,68,.4);
    }
    @media (max-width:576px){
      .search-wrapper .form-control{
        border-radius:999px;
        margin-bottom:.5rem;
      }
      .search-wrapper .btn-search{
        border-radius:999px;
        width:100%;
      }
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <div class="nav-pill-wrapper">
    <nav class="navbar navbar-expand-lg bg-white navbar-rounded">
      <a class="navbar-brand fw-bold me-4" href="#">MTTG</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link fw-semibold text-danger" href="#">Beranda</a>
          </li>
          <li class="nav-item"><a class="nav-link" href="#">Masjid</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Mushalla</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Info Terkini</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Unduh Data</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Kontak Kami</a></li>
        </ul>
        <a href="#" class="btn btn-danger text-white btn-login">Login</a>
      </div>
    </nav>
  </div>

  <!-- HERO -->
  <section class="hero-section">
    <div class="container">
      <div class="hero-card">
        <div class="hero-bg"></div>

        <div class="hero-content">
          <h3 class="hero-title">
            Sistem Informasi Majelis Taklim Telkom Group (MTTG)<br>
            Majelis Taklim Telkom Group (MTTG)
          </h3>
          <p class="hero-subtitle">
            Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)
          </p>

          <!-- SEARCH BAR -->
          <div class="search-wrapper">
            <div class="input-group flex-column flex-sm-row">
              <input type="text" class="form-control" placeholder="cari data masjid / musholla">
              <button class="btn btn-danger btn-search">
                <i class="bi bi-search me-1"></i> Cari Data
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

</body>
</html>
