<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $title ?? 'Admin - Simas MTTG' }}</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  />

  <style>
    :root{
      --bg: #f6f7fb;
      --text: #0f172a; /* slate-900 */
      --muted: #6b7280; /* gray-500 */
      --primary: #ef4444; /* red-500 */
      --card: #ffffff;
      --shadow: 0 10px 25px rgba(2,6,23,.08);
      --radius: 16px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial,'Noto Sans',sans-serif;
      color:var(--text);
      background:var(--bg);
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }

    .container{max-width:1200px;margin:0 auto;padding:24px}
    .logo-left{position: fixed; top: 30px; left: 75px; z-index: 50; display:flex; align-items:center; gap:10px; background: var(--card); border-radius:9999px; padding:8px 14px; box-shadow: var(--shadow);} 
    .logo-left img{ height: 28px; width: 28px; }

    .nav{ position: sticky; top: 12px; z-index: 40; margin-left: auto; margin-right: 24px; width: clamp(640px, 55vw, 920px); }
    .nav-inner{ position: relative; display: flex; align-items: center; justify-content: space-between; background: var(--card); border-radius: 9999px; padding: 10px 14px; box-shadow: var(--shadow); gap: 16px; }
    .hamburger{ display: none; }

    /* small helper -- admin content area */
    .admin-content{ margin-top: 80px; }

    @media (max-width:768px){ .hamburger{ display:inline-flex; } }
  </style>
  {{-- allow pages to push CSS or head tags here --}}
  @stack('head')
</head>
<body>
  {{ $slot }}

  <!-- Bootstrap JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <!-- Optional small script for toggles that are used in admin pages -->
  <script>
    (function(){
      const nav = document.querySelector('.nav');
      if(!nav) return;
      const btn = nav.querySelector('.hamburger');
      const menu = nav.querySelector('#primary-menu');
      if(!btn || !menu) return;

      function toggle(){
        const open = nav.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open);
      }
      btn.addEventListener('click', toggle);
      document.addEventListener('click', (e)=>{
        if(!nav.contains(e.target) && nav.classList.contains('is-open')){
          nav.classList.remove('is-open');
          btn.setAttribute('aria-expanded', 'false');
        }
      });
    })();
  </script>

  {{-- Render any page-specific scripts/styles pushed by child views --}}
  @stack('scripts')

  {{-- Global toasts for flash messages (success / error) --}}
  <div aria-live="polite" aria-atomic="true" style="position:fixed;top:20px;right:20px;z-index:2000">
    <div id="flash-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000" style="min-width:220px">
      <div class="toast-header">
        <strong class="me-auto" id="flash-title">Notice</strong>
        <small class="text-muted"></small>
        <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body" id="flash-body"></div>
    </div>
  </div>

  <script>
    (function(){
      var toastEl = document.getElementById('flash-toast');
      if(!toastEl) return;
      var bsToast = new bootstrap.Toast(toastEl);
  var success = <?php echo json_encode(session('success') ?? null, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  var error = <?php echo json_encode(session('error') ?? null, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
      if(success){ document.getElementById('flash-title').innerText = 'Success'; document.getElementById('flash-body').innerText = success; toastEl.classList.remove('bg-danger'); toastEl.classList.add('bg-white'); bsToast.show(); }
      else if(error){ document.getElementById('flash-title').innerText = 'Error'; document.getElementById('flash-body').innerText = error; toastEl.classList.remove('bg-white'); toastEl.classList.add('bg-danger','text-white'); bsToast.show(); }
    })();
  </script>
</body>
</html>

