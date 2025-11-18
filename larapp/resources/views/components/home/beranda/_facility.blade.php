@props(['masjids' => null, 'mushollas' => null, 'provinces' => null])
<section class="facility-section mt-4">
	<div class="container">
		<div class="row mb-3 align-items-center g-2 prayer-card">
			<div class="col-12 col-md-5 col-lg-5 prayer-card-body">
				<div class="fw-semibold" style="font-size:1.1rem;">Kelengkapan Fasilitas</div>
				<div class="text-muted" style="font-size:.95rem;">Kelengkapan fasilitas masjid / mushalla (Jatim, Bali, Nusra)</div>
			</div>
			<div class="col-12 col-md-7 col-lg-7 prayer-card-body">
				<div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
					<select id="filterProvince" class="form-select form-select-sm w-auto" style="min-width:150px;" aria-label="Pilih provinsi">
						<option value="">Semua Provinsi</option>
						@if(!empty($provinces))
							@foreach($provinces as $p)
								<option value="{{ $p->id }}">{{ $p->name }}</option>
							@endforeach
						@endif
					</select>
					<select id="filterCity" class="form-select form-select-sm w-auto" style="min-width:120px;">
						<option value="">Semua Kota / Kabupaten</option>
						<!-- cities will be populated dynamically when a province is selected -->
					</select>
					<select id="filterCompleteness" class="form-select form-select-sm w-auto" style="min-width:120px;">
						<option value="">Semua Kelengkapan</option>
						<option value="100">100%</option>
						<option value="80">>= 80%</option>
					</select>
				</div>
			</div>
		</div>
		<div class="dual-facility-card fixed-height mb-1">
			<div class="row">
				<div class="split-col col-12 col-lg-6 pe-lg-3">
					<div class="d-flex align-items-center mb-2">
						<i class="bi bi-moon-stars fs-3 me-2 text-secondary"></i>
						<div>
							<div class="fw-bold" style="font-size:1rem;">Data Masjid TREG 3</div>
							<div class="text-muted small">Informasi semua masjid di TREG 3</div>
						</div>
					</div>
					<div class="facility-list">
						<div id="masjidGrid" class="facility-grid">
							@if(isset($masjids) && $masjids->count())
									@foreach($masjids as $m)
										@php
											$img = asset('images/mosque-'.(rand(1,5)).'.png');
											$pct = (int) ($m->completion_percentage ?? 0);
										@endphp
										<div class="facility-card modern shadow-sm">
											<img src="{{ $img }}" alt="Foto {{ $m->name }}" loading="lazy">
											<div class="fc-body">
												<h6 class="fc-title" title="{{ $m->name }}">{{ $m->name }}</h6>
												<div class="fc-sub">{{ trim(($m->province?->name ?? '') . ' / ' . ($m->city?->name ?? '')) }}</div>
												<div class="fc-label">Fasilitas</div>
												<div class="fc-progress-wrap">
													<div class="progress"><div class="progress-bar" role="progressbar" style="width:{{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div></div>
													<div class="fc-pct">{{ $pct }}%</div>
												</div>
											</div>
										</div>
									@endforeach
							@else
								<div class="text-muted">Tidak ada data masjid tersedia.</div>
							@endif
						</div>
					</div>
					<div class="text-center mt-2"><a href="#" class="more-link">Lihat Lebih Lengkap</a></div>
				</div>
				<div class="split-col col-12 col-lg-6 ps-lg-3">
					<div class="d-flex align-items-center mb-2">
						<i class="bi bi-moon-stars fs-3 me-2 text-secondary"></i>
						<div>
							<div class="fw-bold" style="font-size:1.1rem;">Data Mushalla TREG 3</div>
							<div class="text-muted small">Informasi semua mushalla di TREG 3</div>
						</div>
					</div>
					<div class="facility-list">
						<div id="mushollaGrid" class="facility-grid">
							@if(isset($mushollas) && $mushollas->count())
									@foreach($mushollas as $m)
										@php
											$img = asset('images/mosque-'.(rand(1,5)).'.png');
											$pct = (int) ($m->completion_percentage ?? 0);
										@endphp
										<div class="facility-card modern shadow-sm">
											<img src="{{ $img }}" alt="Foto {{ $m->name }}" loading="lazy">
											<div class="fc-body">
												<h6 class="fc-title" title="{{ $m->name }}">{{ $m->name }}</h6>
												<div class="fc-sub">{{ trim(($m->province?->name ?? '') . ' / ' . ($m->city?->name ?? '')) }}</div>
												<div class="fc-label">Fasilitas</div>
												<div class="fc-progress-wrap">
													<div class="progress"><div class="progress-bar" role="progressbar" style="width:{{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div></div>
													<div class="fc-pct">{{ $pct }}%</div>
												</div>
											</div>
										</div>
									@endforeach
							@else
								<div class="text-muted">Tidak ada data mushalla tersedia.</div>
							@endif
						</div>
					</div>
					<div class="text-center mt-2"><a href="#" class="more-link">Lihat Lebih Lengkap</a></div>
				</div>
			</div>
		</div>
	</div>
</section>
