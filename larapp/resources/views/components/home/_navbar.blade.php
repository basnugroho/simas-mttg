<nav class="navbar navbar-expand-lg py-2">
		<div class="container">
			<a class="navbar-brand" href="/">
				<img src="{{ asset('images/logo-mttg.png') }}" alt="MTTG Logo" />
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
				<div class="nav-pill-wrapper px-1 d-flex align-items-center">
					@php
						$menuMap = [
							'beranda' => ['path' => '/', 'pattern' => '/'],
							'masjid' => ['path' => '/masjid', 'pattern' => 'masjid*'],
							'musholla' => ['path' => '#', 'pattern' => 'musholla*'],
							'articles' => ['path' => '#', 'pattern' => 'articles*'],
							'download' => ['path' => '#', 'pattern' => 'download*'],
							'contact' => ['path' => '#', 'pattern' => 'contact*'],
						];
					@endphp
					<ul class="navbar-nav d-flex align-items-center mb-0">
						<li class="nav-item"><a data-menu="beranda" class="nav-link {{ Request::is('/') ? 'text-danger' : '' }}" href="/">Beranda</a></li>
						<li class="nav-item"><a data-menu="masjid" class="nav-link {{ Request::is('masjid*') ? 'text-danger' : '' }}" href="/masjid">Masjid</a></li>
						<li class="nav-item"><a data-menu="musholla" class="nav-link {{ Request::is('musholla*') ? 'text-danger' : '' }}" href="#">Mushalla</a></li>
						<li class="nav-item"><a data-menu="articles" class="nav-link {{ Request::is('articles*') ? 'text-danger' : '' }}" href="#">Info Terkini</a></li>
						<li class="nav-item"><a data-menu="download" class="nav-link {{ Request::is('download*') ? 'text-danger' : '' }}" href="#">Unduh Data</a></li>
						<li class="nav-item"><a data-menu="contact" class="nav-link {{ Request::is('contact*') ? 'text-danger' : '' }}" href="#">Kontak Kami</a></li>
						<li class="nav-item ms-lg-1">
							@auth
								@php $dashboardRoute = Route::has('dashboard') ? route('dashboard') : url('/home'); @endphp
								<a class="btn btn-login-gradient rounded-pill px-3 py-1" href="{{ $dashboardRoute }}">Dashboard</a>
							@else
								@if(Route::has('login'))
									<a class="btn btn-login-gradient rounded-pill px-3 py-1" href="{{ route('login') }}">Login</a>
								@else
									<a class="btn btn-login-gradient rounded-pill px-3 py-1" href="/login">Login</a>
								@endif
							@endauth
						</li>
					</ul>
				</div>
			</div>
			<script>
			// Provide instant visual feedback when a navbar item is clicked
			(function(){
				const nav = document.getElementById('mainNavbar');
				if(!nav) return;
				nav.addEventListener('click', function(e){
					const a = e.target.closest('a[data-menu]');
					if(!a) return;
					const all = nav.querySelectorAll('a[data-menu]');
					all.forEach(x=> x.classList.remove('text-danger'));
					a.classList.add('text-danger');
				});
			})();
			</script>
		</div>
</nav>