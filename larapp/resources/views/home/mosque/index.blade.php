<x-home.layout :title="'Simas MTTG - Detail Masjid'">
	<x-home._navbar />
 	<x-home.beranda._hero>
        <h3 class="hero-title display-6 mb-2">Data Masjid Telkom Regional 3</h3>
			<p class="mb-4 fw-medium" style="font-size:0.95rem">Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)</p>
    </x-home._hero>

	<section class="container my-5">
		<div class="row">
			<aside class="col-md-3 mb-4">
				<div class="card p-3 shadow-sm">
					<h5 class="mb-3">Filter</h5>
					<div class="mb-2">
						<label class="form-label small">Provinsi</label>
						<select class="form-select">
							<option>Pilih Provinsi</option>
						</select>
					</div>
					<div class="mb-2">
						<label class="form-label small">Kota / Kabupaten</label>
						<select class="form-select">
							<option>Pilih Kota / Kabupaten</option>
						</select>
					</div>
					<div class="mb-2">
						<label class="form-label small">Witel</label>
						<select class="form-select">
							<option>Pilih Witel</option>
						</select>
					</div>
					<div class="mb-2">
						<label class="form-label small">Kelengkapan</label>
						<select class="form-select">
							<option>Pilih Kelengkapan</option>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label small">Cari</label>
						<input class="form-control" placeholder="Cari nama / lokasi..." />
					</div>
					<div class="d-flex gap-2">
						<button class="btn btn-light w-50">Reset</button>
						<button class="btn btn-success w-50">Cari</button>
					</div>
				</div>
			</aside>

			<main class="col-md-9">
				<div class="row g-4">
					@for($i = 0; $i < 8; $i++)
						@php
							$imgIndex = rand(1,2);
							$img = asset('images/mosque-' . $imgIndex . '.png');
							$percent = 60 + ($i * 5);
						@endphp
						<div class="col-md-6 col-lg-4">
							<div class="card h-100 shadow-sm">
								<img src="{{ $img }}" class="card-img-top" style="height:150px; object-fit:cover;" alt="Masjid {{ $i + 1 }}">
								<div class="card-body">
									<h6 class="card-title mb-1">Masjid Contoh {{ $i + 1 }}</h6>
									<p class="text-muted small mb-2">Witel Contoh<br/>Jl. Contoh No. 123, Kota</p>
									<div class="small text-danger">Fasilitas</div>
									<div class="progress" style="height:6px;">
										<div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<div class="text-end small text-muted mt-1">{{ $percent }}%</div>
								</div>
							</div>
						</div>
					@endfor
				</div>
			</main>
		</div>
	</section>

	<x-home._footer />
</x-home.layout>

