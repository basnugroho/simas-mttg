<x-home.layout :title="'Simas MTTG - Home'">
    <x-home._navbar />
    <x-home.beranda._hero>
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
    </x-home._hero>
	<x-home.beranda._prayerbar />
	<x-home.beranda._summary :regions="$regions" :summary="$summary" />
	<x-home.beranda._facility :masjids="$masjids" :mushollas="$mushollas" :provinces="$provinces" />

	{{-- Section unduh data masjid & mushalla --}}
	<section class="py-4 py-md-5 bg-light">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6 mb-3 mb-md-0">
					<h5 class="mb-2">Unduh Data Masjid &amp; Mushalla</h5>
					<p class="mb-0" style="font-size:0.95rem">Unduh data lengkap masjid dan mushalla SIMAS MTTG dalam format CSV.</p>
				</div>
				<div class="col-md-6 text-md-end">
					<a href="{{ route('admin.mosques.export', ['type' => 'MASJID']) }}" class="btn btn-sm btn-primary me-2">
						<i class="bi bi-download"></i> Unduh Masjid
					</a>
					<a href="{{ route('admin.mosques.export', ['type' => 'MUSHOLLA']) }}" class="btn btn-sm btn-outline-primary">
						<i class="bi bi-download"></i> Unduh Mushalla
					</a>
				</div>
			</div>
		</div>
	</section>

	<x-home.beranda._map :provinces="$provinces" />
	<x-home.beranda._articles :articles="$latestArticles" :showHeader="true" mtClass="mt-3" :limit="4" />
    <x-home._footer />
</x-home.layout>