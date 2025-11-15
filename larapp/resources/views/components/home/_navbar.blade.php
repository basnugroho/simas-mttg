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
					<ul class="navbar-nav d-flex align-items-center mb-0">
						<li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Masjid</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Mushalla</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Info Terkini</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Unduh Data</a></li>
						<li class="nav-item"><a class="nav-link" href="#">Kontak Kami</a></li>
						<li class="nav-item ms-lg-1">
							<a class="btn btn-login-gradient rounded-pill px-3 py-1" href="{{ route('login') }}">Login</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
</nav>