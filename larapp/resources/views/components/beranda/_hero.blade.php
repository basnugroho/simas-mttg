<main class="container pb-0">
	<div class="hero-card mt-1" style="background-image:url('{{ asset('images/mosque.png') }}')">
		<div class="hero-overlay"></div>
		<div class="hero-content">
			<h4 class="hero-title display-9 mb-2">Sistem Informasi Manajemen Masjid <b>(SIMAS)</b><br> Majelis Taklim Telkom Group <b>(MTTG)</b></h4>
			<p class="mb-4 fw-medium" style="font-size:0.95rem">Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)</p>
			<form action="search" method="GET" class="mx-auto search-form position-relative" autocomplete="off" id="searchForm">
				<div class="search-wrapper d-flex overflow-hidden">
					<input type="text" name="q" class="form-control" placeholder="cari data masjid / musholla" id="searchInput" />
					<button type="submit" class="btn btn-search">
						<i class="bi bi-search"></i><span>Cari Data</span>
					</button>
				</div>
				<div id="autocomplete" class="d-none autocomplete-box"></div>
			</form>
		</div>
	</div>
</main>
