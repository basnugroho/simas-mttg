<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>{{ $title ?? 'Simas MTTG' }}</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
 <link href="{{ asset('css/home.css') }}" rel="stylesheet"/>
</head>
<body>
  <div class="page-wrapper">
    {{-- Navbar for front pages --}}
    <x-home.navbar />

    <main class="container content">
      {{ $slot }}
    </main>

    <footer class="site-footer" role="contentinfo">
      <div class="site-footer-inner">
        © {{ date('Y') }} MTTG Regional 3. All rights reserved.
      </div>
    </footer>
  </div>
  <style>
    html, body { height: 100%; }
    body { display: flex; flex-direction: column; margin: 0; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
    .page-wrapper { min-height: 100vh; display: flex; flex-direction: column; }
    .content { flex: 1 0 auto; }
    .site-footer { flex-shrink: 0; background: #f13030; border-top: 10px solid #1e1e1e; color: #fff; text-align: center; }
    .site-footer-inner { padding: 10px 16px; font-size: 13px; opacity: 0.95; }
    @media (min-width: 768px) {
      .site-footer-inner { padding: 12px 24px; font-size: 14px; }
    }
  </style>

  <script>
    const HERO_URL = '';
    if (HERO_URL) {
      document.querySelector('.hero-media').style.setProperty('--hero', `url(${HERO_URL})`);
    }
  </script>
  </body>
</html>
