<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Auth' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .pink-bg { background: radial-gradient(1200px 600px at 60% 50%, rgba(255,255,255,.35), transparent 60%),
                         linear-gradient(135deg,#ffd1dc 0%,#f8a6ba 100%); }
    .card-dark { background:#111318; }
    .glass-input { background:#1a1d24; border:1px solid #262b36; }
    .glass-input:focus { outline:none; border-color:#ff3b3b; box-shadow:0 0 0 3px rgba(239,68,68,.25); }
    .btn-red { background:#e11d2e; color:white; }
    .btn-red:hover { background:#c11222; color:white; }
    .btn-red:focus { outline:none; box-shadow:0 0 0 4px rgba(225,29,46,0.18); }
  </style>
</head>
<body class="min-h-screen">
  <div class="min-h-screen grid md:grid-cols-2">
    <div class="flex items-center justify-center p-6 md:p-10 bg-black">
      <div class="card-dark text-white rounded-2xl p-8 md:p-12 w-full max-w-md shadow-xl">
        {{ $slot }}
      </div>
    </div>

    <div class="pink-bg relative flex flex-col items-center justify-center p-8">
      <div class="absolute inset-0 pointer-events-none rounded-2xl md:rounded-none border border-pink-200/40 m-2"></div>
      <div class="max-w-lg text-center">
        <img src="{{ asset('images/logo-mttg.png') }}" alt="Logo" class="mx-auto w-56 md:w-72 mb-6">
        <nav class="mt-10 flex items-center justify-center gap-8 text-sm text-rose-900/80">
          <a href="/" class="hover:text-rose-900 font-medium">Home</a>
          <a href="#" class="hover:text-rose-900 font-medium">Masjid</a>
          <a href="#" class="hover:text-rose-900 font-medium">Mushalla</a>
          <a href="#" class="hover:text-rose-900 font-medium">Info Terkini</a>
        </nav>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('click', e => {
      if (e.target.matches('[data-toggle-pass]')) {
        const input = document.querySelector('#password');
        input.type = input.type === 'password' ? 'text' : 'password';
      }
    });
  </script>
</body>
</html>
